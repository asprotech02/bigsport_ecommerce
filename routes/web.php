<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\MidtransController;

// ==========================================
// 1. RUTE PUBLIK (Tidak perlu login)
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/search/live', [ProductController::class, 'liveSearch'])->name('api.search.live');
Route::get('/products', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');

Route::get('/store_location', function () {
    return view('pages.customer.store_location'); 
})->name('store_location');

Route::get('/contact_store', function () {
    return view('pages.customer.contact_store');
})->name('contact_store');

// 👇👇👇 ROUTE WEBHOOK MIDTRANS (AMAN DI LUAR AUTH) 👇👇👇
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
// 👆👆👆 ------------------------------------------ 👆👆👆


// ==========================================
// 2. RUTE PROTECTED (Wajib Login & Verifikasi Email)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Profile & Address
    Route::get('/profile', function () { return view('pages.customer.profile'); })->name('profile');
    Route::get('/profile_edit', function () { return view('pages.customer.profile_edit'); })->name('profile_edit');
    Route::get('/address', function () { return view('pages.customer.address'); })->name('address');
    Route::get('/address_edit', [AddressController::class, 'create'])->name('address_edit');
    Route::post('/address/store', [AddressController::class, 'store'])->name('address.store');
    
    // Notification
    Route::get('/notification', function () { return view('pages.customer.notification'); })->name('notification');

    // Wishlist
    Route::get('/wishlist', [App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist');
    Route::delete('/wishlist/{id}', [App\Http\Controllers\Customer\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/toggle', [App\Http\Controllers\Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');

    // Proses Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/shipping', [CheckoutController::class, 'checkShippingCost'])->name('checkout.check-shipping');
    Route::post('/checkout/check-shipping', [CheckoutController::class, 'checkShippingCost'])->name('checkout.check-shipping');
    Route::post('/checkout/apply-promo', [CheckoutController::class, 'applyPromo'])->name('checkout.promo');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    
    // Order & Tracking
    Route::get('/order', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('order');
    Route::get('/order_success', function () {
        // Ambil 1 pesanan paling baru milik user yang sedang login
        $order = \App\Models\Order::where('user_id', auth()->id())->latest()->first();
        
        // Lempar data $order ke halaman view
        return view('pages.customer.order_success', compact('order'));
    })->name('order_success');
    Route::get('/order_track', function () { return view('pages.customer.order_track'); })->name('order_track');

    // Lain-lain
    Route::get('/chatbot', function () { return view('pages.customer.chatbot'); })->name('chatbot');
    Route::get('/login_edit', function () { return view('pages.customer.auth.login_edit'); })->name('login_edit');

}); // <-- PENUTUP BLOK AUTH

// Memuat rute autentikasi bawaan Laravel Breeze/Breeze-like
require __DIR__.'/auth.php';