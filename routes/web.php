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
use App\Http\Controllers\Customer\ProfileController; // <-- IMPORT BARU UNTUK SPA PROFILE

// ==========================================
// 1. RUTE PUBLIK (Tidak perlu login)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/search/live', [ProductController::class, 'liveSearch'])->name('api.search.live');
Route::get('/products', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');

// Rute ini dibiarkan publik untuk berjaga-jaga jika diakses dari Footer oleh user yang belum login
Route::get('/store_location', function () { return view('customer.pages.store_location'); })->name('store_location');
Route::get('/contact_store', function () { return view('customer.pages.contact_store'); })->name('contact_store');
Route::get('/kebijakan-keamanan-data', function () { return view('customer.pages.privacy'); })->name('privacy');


// Pastikan ini ada di luar middleware auth agar bisa dites manual
Route::get('/api/biteship/search-area', function (Request $request) {
    $input = $request->query('input');
    
    $response = Http::withHeaders([
        'authorization' => env('BITESHIP_API_KEY'),
    ])->get("https://api.biteship.com/v1/maps/areas", [
        'input' => $input,
        'countries' => 'id',
        'limit' => 100 // 🔥 Tambahkan limit ini biar datanya keluar banyak
    ]);

    return $response->json();
});

// 👇👇👇 ROUTE WEBHOOK MIDTRANS (AMAN DI LUAR AUTH) 👇👇👇
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
// 👆👆👆 ------------------------------------------ 👆👆👆

// ==========================================
// 2. RUTE PROTECTED (Wajib Login & Verifikasi Email)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 👇👇👇 RUTE PROFILE YANG SUDAH DIPERBAIKI (SPA) 👇👇👇
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // Rute Edit (Tetap dipisah karena bentuknya form halaman baru)
    Route::get('/profile_edit', function () { return view('customer.pages.profile_edit'); })->name('profile_edit');
    Route::get('/address_edit', [AddressController::class, 'create'])->name('address_edit');
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy');
    Route::patch('/address/{id}/set-main', [AddressController::class, 'setMain'])->name('address.set_main');
    Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');
    Route::get('/login_edit', function () { return view('customer.pages.auth.login_edit'); })->name('login_edit');
    // 👆👆👆 ------------------------------------------ 👆👆👆
    // Route untuk mengambil data tracking via AJAX
Route::get('/profile/order/{id}/tracking', [App\Http\Controllers\Customer\ProfileController::class, 'getTracking'])->name('profile.tracking');
    // Notification
    Route::get('/notification', function () { return view('customer.pages.notification'); })->name('notification');

    // Wishlist
    Route::get('/wishlist', [App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist');
    Route::delete('/wishlist/{id}', [App\Http\Controllers\Customer\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/toggle', [App\Http\Controllers\Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');

    Route::get('/orders', [ProfileController::class, 'index'])->name('order');
    // Proses Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/check-shipping', [CheckoutController::class, 'checkShippingCost'])->name('checkout.check-shipping'); // (Hati-hati ada duplikat rute check-shipping di kodingan asli lu)
    Route::post('/checkout/apply-promo', [CheckoutController::class, 'applyPromo'])->name('checkout.promo');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    
    // Order Success
    Route::get('/order_success', function () {
        // Ambil 1 pesanan paling baru milik user yang sedang login
        $order = \App\Models\Order::where('user_id', auth()->id())->latest()->first();
        
        // Lempar data $order ke halaman view
        return view('customer.pages.order_success', compact('order'));
    })->name('order_success');

    

    // Lain-lain
    Route::get('/chatbot', function () { return view('customer.pages.chatbot'); })->name('chatbot');

}); // <-- PENUTUP BLOK AUTH

// Memuat rute autentikasi bawaan Laravel Breeze/Breeze-like
require __DIR__.'/auth.php';