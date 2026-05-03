<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart; 

class CartController extends Controller
{
    // 1. Menampilkan Halaman Keranjang (MURNI CART)
    public function index()
    {
        $cartItems = Cart::with([
            'productSku.product.brand', 
            'productSku.product.images' => function($q) {
                $q->where('is_primary', true);
            }
        ])->where('user_id', Auth::id())->get();
        
        return view('customer.pages.cart', compact('cartItems'));
    }

    // 2. Fungsi untuk Menyimpan ke Keranjang
    public function store(Request $request)
    {
        $request->validate([
            'sku_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $existingCart = Cart::where('user_id', Auth::id())
                            ->where('product_sku_id', $request->sku_id)
                            ->first();

        if ($existingCart) {
            $existingCart->quantity += $request->quantity;
            $existingCart->save();
        } else {
            // 🌟 VARIABEL $newCartItem SUDAH BENAR DI SINI
            $newCartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_sku_id' => $request->sku_id,
                'quantity' => $request->quantity
            ]);
        }

        // JIKA TOMBOL "BELI SEKARANG" DIKLIK (Normal Redirect)
        if ($request->action == 'buy_now') {
            $cartId = $existingCart ? $existingCart->id : $newCartItem->id; 
            return redirect()->route('checkout', ['cart_ids' => [$cartId]]);
        }

        // 🌟 FIX: Hitung jumlah entri keranjang unik untuk update badge navbar
        $totalCart = Cart::where('user_id', Auth::id())->count();

        // 🌟 FIX: Jika request datang dari JavaScript (Tombol "Tambah Keranjang")
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'total' => $totalCart // Kirim total terbaru ke script JS
            ]);
        }

        // Fallback (jaga-jaga kalau JS mati/gagal)
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    // 3. Fungsi untuk Menghapus Item dari Keranjang
    public function destroy($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang');
        }

        return redirect()->back()->with('error', 'Produk tidak ditemukan');
    }

    // 4. Update Qty AJAX
    public function update(Request $request, $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            $product = $cartItem->productSku->product;
            $price = $product->discount_price ?? $product->base_price;
            $itemSubtotal = $price * $cartItem->quantity;

            return response()->json([
                'success' => true,
                'new_qty' => $cartItem->quantity,
                'item_subtotal' => 'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
            ]);
        }

        return response()->json(['success' => false], 404);
    }
}