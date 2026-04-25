<?php

use Illuminate\Support\Facades\Route;

// <!-- ===========================PERCOBAAN========================== -->
use App\Http\Controllers\HomeController;
// <!-- ===========================PERCOBAAN========================== -->

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/profile', function () {
    return view('pages.customer.profile');
})->name('profile');

Route::get('/profile_edit', function () {
    return view('pages.customer.profile_edit');
})->name('profile_edit');

Route::get('/login_edit', function () {
    return view('pages.customer.auth.login_edit');
})->name('login_edit');

Route::get('/address', function () {
    return view('pages.customer.address');
})->name('address');

Route::get('/store_location', function () {
    return view('pages.customer.store_location'); 
})->name('store_location');

Route::get('/chatbot', function () {
    return view('pages.customer.chatbot');
})->name('chatbot');

Route::get('/contact_store', function () {
    return view('pages.customer.contact_store');
})->name('contact_store');

Route::get('/address_edit', function () {
    return view('pages.customer.address_edit');
})->name('address_edit');

Route::get('/notification', function () {
    return view('pages.customer.notification');
})->name('notification');

Route::get('/wishlist', function () {
    return view('pages.customer.wishlist');
})->name('wishlist');

Route::get('/cart', function () {
    return view('pages.customer.cart');
})->name('cart');

Route::get('/checkout', function () {
    return view('pages.customer.checkout');
})->name('checkout');

Route::get('/detail_product', function () {
    return view('pages.customer.detail_product');
})->name('detail_product');

// Halaman Transaksi & Lacak
Route::get('/order_success', function () {
    return view('pages.customer.order_success');
})->name('order_success');

Route::get('/order', function () {
    return view('pages.customer.order');
})->name('order');

Route::get('/order_track', function () {
    return view('pages.customer.order_track');
})->name('order_track');

require __DIR__.'/auth.php';