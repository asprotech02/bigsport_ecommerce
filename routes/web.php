<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages/customer/home');
});

Route::get('/login', function () {
    return view('pages/customer/login');
})->name('login');

Route::get('/forgot_password', function () {
    return view('pages/customer/forgot_password'); 
})->name('forgot.password');

Route::get('/profile', function () {
    return view('pages.customer.profile');
})->name('profile');

Route::get('/profile_edit', function () {
    return view('pages.customer.profile_edit');
})->name('profile.edit');

Route::get('/login_edit', function () {
    return view('pages.customer.login_edit');
})->name('login_edit');

Route::get('/address', function () {
    return view('pages.customer.address');
})->name('address');

Route::get('/store_location', function () {
    return view('pages.customer.store_location'); 
})->name('store_location');

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


// <!-- ===========================PERCOBAAN========================== -->
use App\Http\Controllers\ProductController;

// Rute ini menerima parameter dinamis via URL query (misal: /products?gender=pria&kategori=sepatu)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/', function () {
    // Siapkan data simulasi untuk ditampilkan di halaman Home (misal: bagian "Produk Terbaru")
    $products = [
        [
            'id' => 1, 'brand' => 'ADIDAS', 'name' => 'ADISTAR CONTROL 5 UNISEX', 'gender_type' => 'Unisex', 'color' => 'Abu-abu', 'price' => 1900000, 'rating' => 4, 'reviews' => 117, 
            'image' => 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
        ],
        [
            'id' => 2, 'brand' => 'NIKE', 'name' => 'MERCURIAL VAPOR 14', 'gender_type' => 'Pria', 'color' => 'Merah', 'price' => 3500000, 'rating' => 5, 'reviews' => 342, 
            'image' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
        ],
        [
            'id' => 3, 'brand' => 'PUMA', 'name' => 'FUTURE Z 1.2', 'gender_type' => 'Pria', 'color' => 'Putih', 'price' => 2800000, 'rating' => 4, 'reviews' => 89, 
            'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
        ],
        [
            'id' => 4, 'brand' => 'ADIDAS', 'name' => 'SAMBA CLASSIC', 'gender_type' => 'Unisex', 'color' => 'Hitam', 'price' => 1500000, 'rating' => 5, 'reviews' => 512, 
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
        ],
    ];

    // Pastikan nama view ini sesuai dengan file home Anda (misal: 'home' atau 'pages.customer.home')
    return view('pages.customer.home', compact('products')); 
});
// <!-- ===========================PERCOBAAN========================== -->

