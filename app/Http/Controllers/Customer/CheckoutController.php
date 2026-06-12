<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Promo;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingDetail;
use App\Models\ProductSku;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cartIds = $request->query('cart_ids') ?? session('selected_cart_ids');

        if ($cartIds && is_array($cartIds)) {
            session(['selected_cart_ids' => $cartIds]);
        }

        if (!$cartIds || !is_array($cartIds)) {
            return redirect()->route('cart')->with('error', 'Pilih produk terlebih dahulu');
        }

        $user = Auth::user();
        $cartItems = Cart::with(['productSku.product.images', 'productSku.product.brand'])
            ->whereIn('id', $cartIds)
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Item tidak ditemukan');
        }

        $defaultAddress = Address::where('user_id', $user->id)->orderByDesc('is_default')->first();
        $allAddresses = Address::where('user_id', $user->id)->orderByDesc('is_default')->get();

        $subtotal = $cartItems->sum(function($item) {
            $price = $item->productSku->discount_price ?? $item->productSku->base_price;
            return $price * $item->quantity;
        });

        return view('customer.pages.checkout', compact('cartItems', 'defaultAddress', 'allAddresses', 'subtotal', 'user'));
    }

    public function checkShippingCost(Request $request)
    {
        $user = Auth::user();
        $address = Address::find($request->address_id);
        $cartIds = $request->cart_ids;
        
        if (!$address || !$cartIds) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $cartItems = Cart::with('productSku.product')
            ->whereIn('id', $cartIds)
            ->where('user_id', $user->id)
            ->get();

        $items = [];
        foreach ($cartItems as $cart) {
            $items[] = [
                'name'        => $cart->productSku->product->name,
                'description' => "Size: " . $cart->productSku->size,
                'value'       => (int)($cart->productSku->discount_price ?? $cart->productSku->base_price),
                'weight'      => 500,
                'quantity'    => (int)$cart->quantity,
            ];
        }

        try {
            $response = Http::withHeaders([
                'authorization' => env('BITESHIP_API_KEY'),
                'content-type'  => 'application/json'
            ])->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_postal_code'      => 15154,
                'destination_postal_code' => (int) $address->postal_code,
                'couriers'                => 'jne,jnt,sicepat',
                'items'                   => $items
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['pricing'])) {
                return response()->json(['success' => true, 'rates' => $result['pricing']]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Biteship Error: ' . ($result['error'] ?? 'Kurir tidak menjangkau kode pos ini')
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function applyPromo(Request $request)
    {
        $code = strtoupper($request->promo_code);
        $subtotal = $request->subtotal;
        $user = Auth::user();

        $promo = Promo::where('code', $code)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak valid']);
        }

        if ($user->usedPromos()->where('promo_id', $promo->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Anda sudah menggunakan kode promo ini']);
        }

        if ($promo->used_count >= $promo->max_usage) {
            return response()->json(['success' => false, 'message' => 'Kuota promo habis']);
        }

        if ($subtotal < $promo->min_order_amount) {
            return response()->json(['success' => false, 'message' => 'Minimal belanja tidak terpenuhi']);
        }

        $discount = ($promo->type == 'fixed') ? $promo->reward : ($promo->reward / 100) * $subtotal;

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil dipasang',
            'discount_amount' => $discount,
            'code' => $promo->code
        ]);
    }

    public function processCheckout(Request $request)
    {
        $user = Auth::user();
        if (!$request->cart_ids || !is_array($request->cart_ids)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        // ==================================================
        // TAHAP 1: DATABASE TRANSACTION (KILAT & CEPAT)
        // ==================================================
        DB::beginTransaction();
        try {
            $cartItems = Cart::whereIn('id', $request->cart_ids)->where('user_id', $user->id)->get();
            if ($cartItems->isEmpty()) throw new \Exception('Produk tidak ditemukan di keranjang');

            $skuIds = $cartItems->pluck('product_sku_id')->toArray();
            sort($skuIds); // Anti-Deadlock

            $lockedSkus = ProductSku::with('product')
                            ->whereIn('id', $skuIds)
                            ->lockForUpdate()
                            ->get()
                            ->keyBy('id');

            $totalProductPrice = 0;
            $biteshipItems = [];
            
            foreach ($cartItems as $item) {
                $sku = $lockedSkus->get($item->product_sku_id);
                
                if (!$sku || $sku->available_stock < $item->quantity) {
                    throw new \Exception('Maaf, stok ' . ($sku->product->name ?? 'produk ini') . ' tidak mencukupi.');
                }

                // Reserve Stock
                $sku->reserved_stock += $item->quantity;
                $sku->save();

                $price = $sku->discount_price ?? $sku->base_price;
                $totalProductPrice += $price * $item->quantity;

                $biteshipItems[] = [
                    'name' => $sku->product->name,
                    'description' => "Size: " . $sku->size,
                    'value' => (int) $price,
                    'weight' => 500,
                    'quantity' => (int) $item->quantity
                ];
            }

            $shippingCost = 0;
            $addressId = null;
            $shippingAddressSnapshot = null;

            if ($request->delivery_type === 'delivery') {
                $address = Address::where('id', $request->address_id)->where('user_id', $user->id)->first();
                if (!$address) throw new \Exception('Alamat tidak valid');

                if (!$request->filled('courier_company') || !$request->filled('courier_type')) {
                    throw new \Exception('Silakan pilih kurir dan layanan pengiriman terlebih dahulu.');
                }

                $shippingCost = (int) preg_replace('/[^0-9]/', '', $request->shipping_cost);
                $addressId = $address->id;
                
                // PERBAIKAN: Menggabungkan semua komponen alamat sesuai kolom database
                $addressParts = array_filter([
                    $address->full_address, 
                    $address->village_name ?? null,
                    $address->district_name ?? null,
                    $address->city_name ?? null,
                    $address->province_name ?? null,
                    $address->postal_code ?? null
                ]);
                $completeAddress = implode(', ', $addressParts);

                $shippingAddressSnapshot = $address->receiver_name . " (" . $address->receiver_phone . ") | " . $completeAddress;
            } else {
                // 🌟 FIX: Hardcode alamat toko utama untuk keamanan (mencegah modifikasi elemen HTML oleh user)
                $shippingAddressSnapshot = "Ambil di Toko Utama: Jl. HOS Cokroaminoto No.52, Larangan, Kota Tangerang";
            }

            $discountAmount = 0;
            $promoId = null;
            if ($request->filled('promo_code')) {
                $promo = Promo::where('code', strtoupper($request->promo_code))->where('is_active', true)->lockForUpdate()->first();
                if ($promo) {
                    if ($user->usedPromos()->where('promo_id', $promo->id)->exists()) {
                        throw new \Exception('Anda sudah menggunakan kode promo ini.');
                    }
                    if ($promo->used_count >= $promo->max_usage) {
                        throw new \Exception('Maaf, kuota kode promo sudah habis diklaim.');
                    }
                    $promoId = $promo->id;
                    $discountAmount = $promo->type == 'fixed' ? $promo->reward : ($promo->reward / 100) * $totalProductPrice;
                    $promo->increment('used_count');
                    $user->usedPromos()->attach($promo->id);
                }
            }

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $grandTotal = ($totalProductPrice + $shippingCost) - $discountAmount;
            if ($grandTotal < 0) $grandTotal = 0;

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'shipping_address' => $shippingAddressSnapshot,
                'promo_id' => $promoId,
                'invoice_number' => $invoiceNumber,
                'total_product_price' => $totalProductPrice,
                'total_shipping_cost' => $shippingCost,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            $shippingDetail = ShippingDetail::create([
                'order_id' => $order->id,
                'courier_company' => $request->delivery_type === 'pickup' ? 'pickup' : strtolower($request->courier_company),
                // 🌟 FIX: Langsung set courier type jadi 'toko_utama' jika pickup
                'courier_type' => $request->delivery_type === 'pickup' ? 'toko_utama' : strtolower($request->courier_type),
                'cost' => $shippingCost,
            ]);

            foreach ($cartItems as $item) {
                $sku = $lockedSkus->get($item->product_sku_id);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_sku_id' => $item->product_sku_id,
                    'product_name' => $sku->product->name,
                    'product_size' => $sku->size,
                    'price_at_purchase' => $sku->discount_price ?? $sku->base_price,
                    'quantity' => $item->quantity,
                ]);
            }

            Cart::whereIn('id', $request->cart_ids)->delete();

            // Handle Free Orders directly (Rp 0 total)
            if ($grandTotal <= 0) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'payment_type' => 'promo'
                ]);

                // Deduct stock immediately
                foreach ($cartItems as $item) {
                    $sku = $lockedSkus->get($item->product_sku_id);
                    $sku->stock -= $item->quantity;
                    $sku->reserved_stock -= $item->quantity;
                    $sku->save();
                }

                // Create notification
                \App\Models\UserNotification::create([
                    'user_id' => $user->id,
                    'type'    => 'transaksi',
                    'title'   => 'Pembayaran Berhasil',
                    'message' => "Pembayaran pesanan #{$order->invoice_number} sebesar Rp 0 telah kami terima. Pesanan Anda akan segera dikemas."
                ]);

                DB::commit();
                return response()->json(['success' => true, 'is_free' => true, 'invoice' => $invoiceNumber]);
            }

            // COMMIT: Lepaskan gembok database SEKARANG
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout DB Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        // ==================================================
        // TAHAP 2: CALL API EXTERNAL (HANYA MIDTRANS)
        // ==================================================
        try {
            // Hit API Midtrans
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => ['order_id' => $invoiceNumber, 'gross_amount' => (int)$grandTotal],
                'customer_details' => ['first_name' => $user->name, 'email' => $user->email],
                'callbacks' => [
                    'finish' => route('order_success') . '?order_id=' . $invoiceNumber
                ]
            ]);

            // Update token ke order yang sudah tersimpan
            $order->update(['snap_token' => $snapToken]);
            
            return response()->json(['success' => true, 'snap_token' => $snapToken, 'invoice' => $invoiceNumber]);

        } catch (\Exception $e) {
            // COMPENSATION LOGIC: Jika Midtrans Error, Batalkan Order & Kembalikan Stok!
            Log::error('API External Error (Midtrans): ' . $e->getMessage());
            
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed'
            ]);

            // Kembalikan reserved stock
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            foreach ($orderItems as $item) {
                ProductSku::where('id', $item->product_sku_id)->decrement('reserved_stock', $item->quantity);
            }

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}