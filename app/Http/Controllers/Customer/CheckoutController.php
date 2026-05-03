<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; // Penting untuk debugging
use App\Models\Cart;
use App\Models\Address;
use App\Models\Promo;
use App\Models\Order;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout dengan item yang dipilih
     */
    public function index(Request $request) 
    {
        $cartIds = $request->query('cart_ids');

        // Logic Session Pengingat
        if ($cartIds && is_array($cartIds)) {
            session(['selected_cart_ids' => $cartIds]);
        } else {
            $cartIds = session('selected_cart_ids');
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
            return redirect()->route('cart')->with('error', 'Item tidak ditemukan atau keranjang kosong');
        }

        $defaultAddress = Address::where('user_id', $user->id)
            ->where('is_default', true)
            ->first() ?? Address::where('user_id', $user->id)->first();

        $allAddresses = Address::where('user_id', $user->id)->orderBy('is_default', 'desc')->get();

        $subtotal = $cartItems->sum(function($item) {
            $price = $item->productSku->product->discount_price ?? $item->productSku->product->base_price;
            return $price * $item->quantity;
        });

        return view('customer.pages.checkout', compact(
            'cartItems', 
            'defaultAddress', 
            'allAddresses',
            'subtotal', 
            'user'
        ));
    }

    /**
     * Mengecek ongkos kirim ke API Biteship
     */
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
                'value'       => (int)($cart->productSku->product->discount_price ?? $cart->productSku->product->base_price),
                'weight'      => 500, 
                'quantity'    => (int)$cart->quantity,
            ];
        }

        try {
            // 🌟 FIX 1: KEMBALIKAN KE POSTAL CODE (Agar Origin & Destination sepasang formatnya)
            $response = Http::withHeaders([
                'authorization' => env('BITESHIP_API_KEY'),
                'content-type'  => 'application/json'
            ])->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_postal_code'      => 15710, // Kode pos toko lu
                'destination_postal_code' => (int) $address->postal_code, // Kode pos pembeli
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

    /**
     * Memproses promo belanja
     */
    public function applyPromo(Request $request)
    {
        $code = $request->promo_code;
        $subtotal = $request->subtotal;

        $promo = Promo::where('code', $code)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak valid']);
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

    /**
     * Memproses Checkout, Simpan ke DB, dan Panggil Midtrans & Biteship
     */
    public function processCheckout(Request $request)
    {
        $user = Auth::user();
        
        if (!$request->cart_ids || !is_array($request->cart_ids)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        DB::beginTransaction();
        try {
            $cartItems = Cart::with('productSku.product')
                ->whereIn('id', $request->cart_ids)
                ->where('user_id', $user->id)
                ->get();
            
            if ($cartItems->isEmpty()) throw new \Exception('Produk tidak ditemukan');

            $totalProductPrice = 0;
            $biteshipItems = []; 
            foreach ($cartItems as $item) {
                $price = $item->productSku->product->discount_price ?? $item->productSku->product->base_price;
                $totalProductPrice += $price * $item->quantity;

                $biteshipItems[] = [
                    'name' => $item->productSku->product->name,
                    'description' => "Size: " . $item->productSku->size,
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

                $shippingCost = (int) preg_replace('/[^0-9]/', '', $request->shipping_cost);
                $addressId = $address->id;
                $shippingAddressSnapshot = $address->receiver_name . " (" . $address->receiver_phone . ") | " . $address->full_address;
            } else {
                $shippingAddressSnapshot = "Ambil di Toko: " . strtoupper($request->store_location);
            }

            // Hitung Promo
            $discountAmount = 0;
            $promoId = null;
            if ($request->filled('promo_code')) {
                $promo = Promo::where('code', $request->promo_code)->where('is_active', true)->first();
                if ($promo) {
                    $promoId = $promo->id;
                    $discountAmount = $promo->type == 'fixed' ? $promo->reward : ($promo->reward / 100) * $totalProductPrice;
                    $promo->increment('used_count');
                }
            }

            $grandTotal = ($totalProductPrice + $shippingCost) - $discountAmount;
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // Simpan Order
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
                'courier_company' => $request->delivery_type === 'pickup' ? 'pickup' : $request->courier_company,
                'courier_type' => $request->delivery_type === 'pickup' ? $request->store_location : $request->courier_type,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_sku_id' => $item->product_sku_id,
                    'product_name' => $item->productSku->product->name,
                    'product_size' => $item->productSku->size,
                    'price_at_purchase' => $item->productSku->product->discount_price ?? $item->productSku->product->base_price,
                    'quantity' => $item->quantity,
                ]);
            }

            // 7. PROSES KE BITESHIP (Hanya jika delivery)
            if ($request->delivery_type === 'delivery') {
                
                $cleanSenderPhone = '08123456789'; 
                $cleanReceiverPhone = preg_replace('/[^0-9]/', '', $address->receiver_phone);
                if (strlen($cleanReceiverPhone) < 10) {
                    $cleanReceiverPhone = '08' . str_pad($cleanReceiverPhone, 8, '0', STR_PAD_RIGHT); 
                }

                $biteshipPayload = [
                    'shipper_contact_name'  => 'Big Sport Tangerang',
                    'shipper_contact_phone' => $cleanSenderPhone,
                    'origin_contact_name'   => 'Big Sport Tangerang',
                    'origin_contact_phone'  => $cleanSenderPhone,
                    'origin_address'        => 'Jl. HOS Cokroaminoto No.52, Larangan, Tangerang',
                    
                    // 🌟 FIX: Toko pakai Kode Pos
                    'origin_postal_code'    => 15710, 

                    'destination_contact_name'  => $address->receiver_name,
                    'destination_contact_phone' => $cleanReceiverPhone,
                    'destination_address'       => $address->full_address,
                    
                    // 🌟 FIX: Pembeli WAJIB diseimbangkan pakai Kode Pos juga
                    'destination_postal_code'   => (int) $address->postal_code,
                    // Kita matikan pengiriman destination_area_id agar Biteship tidak bingung
                    // 'destination_area_id'    => $address->district_id, 

                    'courier_company' => strtolower($request->courier_company),
                    'courier_type'    => strtolower($request->courier_type),

                    'items'         => $biteshipItems,
                    'delivery_type' => 'now',
                    'order_note'    => 'Order ' . $invoiceNumber
                ];

                $biteshipResponse = Http::withHeaders([
                    'authorization' => env('BITESHIP_API_KEY'),
                    'content-type' => 'application/json'
                ])->post('https://api.biteship.com/v1/orders', $biteshipPayload);

                if ($biteshipResponse->successful()) {
                    $biteshipData = $biteshipResponse->json();
                    $order->update([
                        'biteship_order_id' => $biteshipData['id'],
                        'waybill_id' => $biteshipData['courier']['waybill_id'] ?? null
                    ]);
                } else {
                    // Jika API Sandbox nolak karena rute (biasanya kode 40002021)
                    $dummyBiteshipId = 'bts_dummy_' . uniqid(); 
                    
                    $order->update([
                        'biteship_order_id' => $dummyBiteshipId,
                        'waybill_id' => 'RESI-' . strtoupper(Str::random(10)) 
                    ]);
                    
                    Log::warning('Biteship Sandbox Reject (' . $biteshipResponse->status() . '). Bypass Activated. Dummy ID: ' . $dummyBiteshipId);
                }
            }

            // Midtrans Logic
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => ['order_id' => $invoiceNumber, 'gross_amount' => (int)$grandTotal],
                'customer_details' => ['first_name' => $user->name, 'email' => $user->email],
            ]);

            $order->update(['snap_token' => $snapToken]);
            Cart::whereIn('id', $request->cart_ids)->delete();

            DB::commit();
            return response()->json(['success' => true, 'snap_token' => $snapToken, 'invoice' => $invoiceNumber]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}