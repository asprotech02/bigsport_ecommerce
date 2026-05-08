<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\MidtransController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\OrderController; // <-- Import Controller untuk Detail Pesanan

// ==========================================
// 1. RUTE PUBLIK (Dapat diakses tanpa login)
// ==========================================

// Rute halaman utama dan pencarian produk
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/search/live', [ProductController::class, 'liveSearch'])->name('api.search.live');
Route::get('/products', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');

// Rute informasi statis (Biasanya dipanggil dari Footer)
Route::get('/store_location', function () { return view('customer.pages.store_location'); })->name('store_location');
Route::get('/contact_store', function () { return view('customer.pages.contact_store'); })->name('contact_store');
Route::get('/kebijakan-keamanan-data', function () { return view('customer.pages.privacy'); })->name('privacy');

// Rute API Publik untuk mencari area kecamatan dari Biteship
Route::get('/api/biteship/search-area', function (Request $request) {
    $input = $request->query('input');
    
    $response = Http::withHeaders([
        'authorization' => env('BITESHIP_API_KEY'),
    ])->get("https://api.biteship.com/v1/maps/areas", [
        'input' => $input,
        'countries' => 'id',
        'limit' => 100
    ]);

    return $response->json();
});



// ==========================================
// 2. RUTE WEBHOOK (Sistem ke Sistem)
// ==========================================

// Webhook Midtrans (Wajib ditaruh di luar middleware auth & tanpa CSRF agar Midtrans bisa masuk)
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);


// ==========================================
// 3. RUTE PROTECTED (Wajib Login & Verifikasi Email)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- BAGIAN PROFIL & AKUN ---
    // Halaman Profil Utama (Single Page Application - Tab)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // --- EDIT EMAIL & PASSWORD DARI PROFIL ---
    Route::get('/email_edit', function () { 
        return view('customer.pages.auth.email_edit', ['user' => auth()->user()]); 
    })->name('email_edit');

    // 🌟 Mengarah ke form reset_password
    Route::get('/password_edit', function () { 
        // Pastikan route view ini sesuai dengan lokasi asli reset_password.blade.php lu!
        // Jika lokasinya di luar folder auth, sesuaikan jadi 'customer.pages.reset_password'
        return view('customer.pages.auth.reset_password', ['user' => auth()->user()]); 
    })->name('password_edit');
    
    // 🌟 Rute Penerima Data Ganti Password
    Route::patch('/password/update-profile', [ProfileController::class, 'updatePassword'])->name('password.update.profile');
    
    // 🌟 FIX TYPO: Halaman Edit Profil (Passing data user dengan sintaks array '=>' yang benar)
    Route::get('/profile_edit', function () { 
        return view('customer.pages.profile_edit', ['user' => auth()->user()]); 
    })->name('profile_edit');
    
    // Action untuk memproses update profil ke Database
    Route::patch('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update'); 
    
    // 🌟 FIX TYPO: Halaman Edit Login (Passing data user dengan sintaks array '=>' yang benar)
    Route::get('/login_edit', function () { 
        return view('customer.pages.login_edit', ['user' => auth()->user()]); 
    })->name('login_edit');




    // --- BAGIAN ALAMAT PENGIRIMAN ---
    Route::get('/address_form', [AddressController::class, 'create'])->name('address_form'); // Halaman Form Alamat
    Route::post('/address/store', [AddressController::class, 'store'])->name('address.store'); // Action Simpan Alamat
    Route::patch('/address/{id}/set-main', [AddressController::class, 'setMain'])->name('address.set_main'); // Action Set Alamat Utama
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy'); // Action Hapus Alamat
    // Pastikan rute ini ada di dalam group middleware auth
    Route::get('/address/create', [AddressController::class, 'create'])->name('address.create'); // Tambah
    Route::get('/address/{id}/edit', [AddressController::class, 'edit'])->name('address.edit'); // Edit
    Route::post('/address/store', [AddressController::class, 'store'])->name('address.store'); // Simpan Baru
    Route::put('/address/{id}/update', [AddressController::class, 'update'])->name('address.update'); // Simpan Perubahan


    // --- BAGIAN PESANAN & TRACKING ---
    Route::get('/orders', [ProfileController::class, 'index'])->name('order'); // Mengarah ke Tab Pesanan di Halaman Profil
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.detail'); // Halaman Rincian Pesanan
    Route::get('/profile/order/{id}/tracking', [ProfileController::class, 'getTracking'])->name('order.tracking'); // Endpoint AJAX Cek Resi
    Route::get('/profile/order/{id}/invoice', [ProfileController::class, 'printInvoice'])->name('order.invoice'); // Endpoint Download PDF Invoice


    // --- BAGIAN WISHLIST ---
    Route::get('/wishlist', [App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [App\Http\Controllers\Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle'); // Tambah/Hapus Wishlist
    Route::delete('/wishlist/{id}', [App\Http\Controllers\Customer\WishlistController::class, 'destroy'])->name('wishlist.destroy');


    // --- BAGIAN KERANJANG BELANJA (CART) ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add'); // Masuk Keranjang
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update'); // Update Qty (+ / -)
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');


    // --- BAGIAN CHECKOUT TRANSAKSI ---
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/check-shipping', [CheckoutController::class, 'checkShippingCost'])->name('checkout.check-shipping'); // AJAX Cek Ongkir Biteship
    Route::post('/checkout/apply-promo', [CheckoutController::class, 'applyPromo'])->name('checkout.promo'); // AJAX Pasang Kupon
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process'); // Action Simpan ke DB & Hit Midtrans
    

    // --- BAGIAN ORDER SUCCESS ---
    Route::get('/order_success', function () {
        // Ambil 1 pesanan paling baru milik user yang sedang login untuk ditampilkan infonya
        $order = \App\Models\Order::where('user_id', auth()->id())->latest()->first();
        return view('customer.pages.order_success', compact('order'));
    })->name('order_success');


// --- LAIN-LAIN ---
    // 🌟 FIX: Rute yang benar, diarahkan ke fungsi 'notifications' (pakai 's') di ProfileController
    Route::get('/notification', [ProfileController::class, 'notifications'])->name('notification');
    
    Route::get('/chatbot', function () { return view('customer.pages.chatbot'); })->name('chatbot');

}); // <-- PENUTUP BLOK AUTH

// Memuat rute autentikasi bawaan Laravel (Login, Register, Reset Password)
require __DIR__.'/auth.php';