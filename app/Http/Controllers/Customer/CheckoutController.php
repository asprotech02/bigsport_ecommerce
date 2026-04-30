<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Promo;
use App\Models\Order;
use App\Models\OrderItem;


class CheckoutController extends Controller
{
    // FIX 1: Tambahkan (Request $request) di sini
    public function index(Request $request) 
    {
        $cartIds = $request->query('cart_ids');

        if (!$cartIds || !is_array($cartIds)) {
            return redirect()->route('cart')->with('error', 'Pilih produk terlebih dahulu');
        }

        $user = Auth::user();
        
        // FIX 2: Gabungkan logic. Ambil data yang dicentang DAN milik user yang login. 
        // Jangan ditimpa lagi di bawahnya.
        $cartItems = Cart::with(['productSku.product.images', 'productSku.product.brand'])
            ->whereIn('id', $cartIds) 
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Item tidak ditemukan atau keranjang kosong');
        }

        // Ambil alamat default untuk tampilan awal
        $defaultAddress = Address::where('user_id', $user->id)
            ->where('is_default', true)
            ->first() ?? Address::where('user_id', $user->id)->first();

        // TAMBAHKAN INI: Ambil SEMUA alamat user untuk ditampilkan di Modal
        $allAddresses = Address::where('user_id', $user->id)->orderBy('is_default', 'desc')->get();

        // Kalkulasi Subtotal berdasarkan item yang dipilih saja
        $subtotal = $cartItems->sum(function($item) {
            $price = $item->productSku->product->discount_price ?? $item->productSku->product->base_price;
            return $price * $item->quantity;
        });

        return view('pages.customer.checkout', compact(
            'cartItems', 
            'defaultAddress', 
            'allAddresses', // <-- Pastikan ini dikirim
            'subtotal', 
            'user'
        ));
    }


    public function checkShippingCost(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $address = \App\Models\Address::find($request->address_id);
        $cartIds = $request->cart_ids; 
        
        if (!$address || !$cartIds) {
            return response()->json(['success' => false, 'message' => 'Data alamat atau produk tidak lengkap']);
        }

        $cartItems = \App\Models\Cart::with('productSku.product')
            ->whereIn('id', $cartIds)
            ->where('user_id', $user->id)
            ->get();

        $items = [];
        foreach ($cartItems as $cart) {
            $items[] = [
                'name'        => $cart->productSku->product->name,
                'description' => "Size: " . $cart->productSku->size,
                'value'       => (int)($cart->productSku->product->discount_price ?? $cart->productSku->product->base_price),
                'weight'      => 1000, 
                'quantity'    => (int)$cart->quantity,
            ];
        }

        try {
            // FIX: Tambahkan "/couriers" di akhir URL dan pastikan Kode Pos jadi angka (int)
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'authorization' => env('BITESHIP_API_KEY'),
                'content-type'  => 'application/json'
            ])->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_postal_code'      => 15115, 
                'destination_postal_code' => (int) $address->postal_code, // Paksa jadi Integer
                'couriers'                => 'jne,jnt,sicepat', 
                'items'                   => $items
            ]);

            $result = $response->json();

            // FIX: Cek apakah HTTP statusnya 200 OK dan pricing-nya ada
            if ($response->successful() && isset($result['pricing'])) {
                return response()->json(['success' => true, 'rates' => $result['pricing']]);
            } else {
                // Munculkan pesan error ASLI dari Biteship biar kita tahu masalahnya apa
                $errorMsg = $result['error'] ?? 'Terjadi kesalahan pada sistem kurir';
                return response()->json(['success' => false, 'message' => 'API Error: ' . $errorMsg]);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }



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
            return response()->json(['success' => false, 'message' => 'Kode promo tidak valid atau sudah kadaluwarsa']);
        }

        if ($promo->used_count >= $promo->max_usage) {
            return response()->json(['success' => false, 'message' => 'Kuota promo sudah habis']);
        }

        if ($subtotal < $promo->min_order_amount) {
            return response()->json(['success' => false, 'message' => 'Minimal belanja Rp ' . number_format($promo->min_order_amount, 0, ',', '.') . ' untuk kode ini']);
        }

        // Hitung potongan
        $discount = 0;
        if ($promo->type == 'fixed') {
            $discount = $promo->reward;
        } else {
            $discount = ($promo->reward / 100) * $subtotal;
        }

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
        
        // 1. Validasi Input Dasar
        if (!$request->cart_ids || !is_array($request->cart_ids)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        DB::beginTransaction();
        try {
            // 2. Ambil Data Cart & Hitung Ulang
            $cartItems = Cart::with('productSku.product')
                ->whereIn('id', $request->cart_ids)
                ->where('user_id', $user->id)
                ->get();
            
            if ($cartItems->isEmpty()) {
                throw new \Exception('Produk tidak ditemukan di keranjang');
            }

            $totalProductPrice = 0;
            foreach ($cartItems as $item) {
                $price = $item->productSku->product->discount_price ?? $item->productSku->product->base_price;
                $totalProductPrice += $price * $item->quantity;
            }

            // 3. Kalkulasi Ongkir & Keamanan Alamat
            $shippingCost = 0;
            $addressId = null;
            $customerPhone = '080000000000'; 

            if ($request->delivery_type === 'delivery') {
                $address = Address::where('id', $request->address_id)->where('user_id', $user->id)->first();
                
                if (!$address) {
                    throw new \Exception('Alamat pengiriman tidak ditemukan atau tidak valid');
                }
                
                // Pastikan shipping_cost dipaksa jadi integer yang solid
                $shippingCost = (int) preg_replace('/[^0-9]/', '', $request->shipping_cost);
                
                if ($shippingCost <= 0 && $request->courier_company != 'pickup') {
                    throw new \Exception('Ongkos kirim belum dikalkulasi');
                }

                $addressId = $address->id;
                $customerPhone = $address->receiver_phone;
            } else {
                // Kalau pickup
                $customerPhone = $user->phone ?? '080000000000';
            }

            // 4. Kalkulasi Promo
            $discountAmount = 0;
            $promoId = null;
            
            if ($request->filled('promo_code')) {
                $promo = Promo::where('code', $request->promo_code)
                    ->where('is_active', true)
                    ->where(function($query) {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->first();
                    
                if (!$promo) {
                    throw new \Exception('Kode promo tidak valid atau sudah kadaluwarsa');
                }
                if ($promo->used_count >= $promo->max_usage) {
                    throw new \Exception('Kuota pemakaian promo sudah habis');
                }
                if ($totalProductPrice < $promo->min_order_amount) {
                    throw new \Exception('Minimal belanja tidak terpenuhi untuk promo ini');
                }

                $promoId = $promo->id;
                $discountAmount = $promo->type == 'fixed' ? $promo->reward : ($promo->reward / 100) * $totalProductPrice;

                // Kunci kuota promo
                $promo->increment('used_count');
            }

            // 5. Grand Total Murni
            $grandTotal = ($totalProductPrice + $shippingCost) - $discountAmount;
            if ($grandTotal < 0) $grandTotal = 0;

            // 6. Generate Invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));

            // 7. Simpan ke table orders
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
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

            // 8. Simpan ke table order_items
            foreach ($cartItems as $item) {
                $product = $item->productSku->product;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_sku_id' => $item->product_sku_id,
                    'product_name' => $product->name,
                    'product_size' => $item->productSku->size,
                    'price_at_purchase' => $product->discount_price ?? $product->base_price,
                    'quantity' => $item->quantity,
                ]);
            }

            // 9. BYPASS MIDTRANS JIKA TRANSAKSI GRATIS
            if ($grandTotal <= 0) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
                Cart::whereIn('id', $request->cart_ids)->delete();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'is_free' => true,
                    'invoice' => $invoiceNumber
                ]);
            }

            // 10. GENERATE SNAP TOKEN (Pastikan environment keys ada)
            if(!config('midtrans.server_key') || !config('midtrans.client_key')) {
                throw new \Exception("Midtrans API Keys belum diatur di file .env");
            }

            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $invoiceNumber,
                    'gross_amount' => (int) $grandTotal,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $customerPhone,
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update token di order
            $order->update(['snap_token' => $snapToken]);

            // Hapus item dari cart
            Cart::whereIn('id', $request->cart_ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'is_free' => false,
                'snap_token' => $snapToken,
                'invoice' => $invoiceNumber
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Catat error aslinya ke log file, lalu tampilkan ke frontend
            \Illuminate\Support\Facades\Log::error("Checkout Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}