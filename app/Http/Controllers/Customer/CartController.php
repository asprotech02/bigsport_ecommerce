<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cart; 

class CartController extends Controller
{
    // 1. Menampilkan Halaman Keranjang
    public function index()
    {
        // AMBIL DATA DARI DATABASE (Ini yang tadi kurang)
        $cartItems = Cart::with([
            'productSku.product.brand', 
            'productSku.product.images' => function($q) {
                $q->where('is_primary', true);
            }
        ])->where('user_id', Auth::id())->get();
        
        // LEMPAR DATA KE VIEW MENGGUNAKAN compact()
        return view('pages.customer.cart', compact('cartItems'));
    }

    // 2. Fungsi untuk Menyimpan ke Keranjang
    public function store(Request $request)
    {
        // Validasi data yang dikirim dari form detail produk
        $request->validate([
            'sku_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        // Cek apakah produk dengan ukuran (SKU) yang sama udah ada di keranjang user ini
        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_sku_id', $request->sku_id)
                            ->first();

        if ($existingCart) {
            // Kalau udah ada, tinggal tambahin jumlahnya (quantity)
            $existingCart->quantity += $request->quantity;
            $existingCart->save();
        } else {
            // Kalau belum ada, bikin baris baru di keranjang
            Cart::create([
                'user_id' => Auth::id(),
                'product_sku_id' => $request->sku_id,
                'quantity' => $request->quantity
            ]);
        }

        // Kalau usernya klik "Beli Sekarang", langsung lempar ke checkout
        if ($request->action == 'buy_now') {
            return redirect()->route('checkout');
        }

        // Kalau klik "Tambah ke Keranjang", balikin lagi ke halaman produk dengan pesan sukses
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    // 3. Fungsi untuk Menghapus Item dari Keranjang
    public function destroy($id)
    {
        // Cari item keranjang berdasarkan ID dan pastikan itu milik user yang sedang login
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang');
        }

        return redirect()->back()->with('error', 'Produk tidak ditemukan');
    }


    public function update(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            // Hitung ulang subtotal untuk dikirim ke layar
            $product = $cartItem->productSku->product;
            $price = $product->discount_price ?? $product->base_price;
            $itemSubtotal = $price * $cartItem->quantity;

            // Berikan respon JSON (Bukan Redirect)
            return response()->json([
                'success' => true,
                'new_qty' => $cartItem->quantity,
                'item_subtotal' => 'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}