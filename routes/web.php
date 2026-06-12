<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\MidtransController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\OrderController; 

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;   
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PageController;

// ==========================================
// RUTE ADMIN (Terproteksi Middleware Keamanan)
// ==========================================
Route::prefix('admin')->middleware(['auth', 'role:admin,sales,manager'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::resource('brands', BrandController::class)
        ->names('admin.brands');

    Route::resource('categories', CategoryController::class)
        ->names('admin.categories');
    
    Route::resource('subcategories', SubcategoryController::class)
        ->names('admin.subcategories');

    Route::resource('products', AdminProductController::class)
        ->names('admin.products');

    // Pelanggan
    Route::get('/customers', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::post('/customers/{id}/toggle', [CustomerController::class, 'toggleStatus'])->name('admin.customers.toggle');

    // Pesanan
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'printInvoice'])->name('admin.orders.invoice');
    Route::match(['put', 'patch', 'post'], '/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::delete('/orders/{id}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');

    // Promo
    Route::resource('promos', PromoController::class)->names('admin.promos');

    // Pengiriman
    Route::get('/shippings', [ShippingController::class, 'index'])->name('admin.shippings.index');
    Route::get('/shippings/{id}/edit', [ShippingController::class, 'edit'])->name('admin.shippings.edit');
    Route::put('/shippings/{id}', [ShippingController::class, 'update'])->name('admin.shippings.update');
    Route::post('/shippings/{id}/book-biteship', [ShippingController::class, 'bookBiteship'])->name('admin.shippings.bookBiteship');
    Route::post('/shippings/book-biteship-bulk', [ShippingController::class, 'bookBiteshipBulk'])->name('admin.shippings.bookBiteshipBulk');
    Route::post('/shippings/complete-bulk', [ShippingController::class, 'completeBulk'])->name('admin.shippings.completeBulk');
    Route::get('/shippings/{id}/label', [ShippingController::class, 'printLabel'])->name('admin.shippings.label');
    Route::get('/shippings/{id}/track', [ShippingController::class, 'trackShipment'])->name('admin.shippings.track');

    // Pick Up (Ambil di Toko)
    Route::get('/pickups', [ShippingController::class, 'indexPickup'])->name('admin.pickups.index');

    // Pembayaran
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::post('/payments/sync', [PaymentController::class, 'syncAll'])->name('admin.payments.sync');
    Route::patch('/payments/{id}/status', [PaymentController::class, 'updateStatus'])->name('admin.payments.updateStatus');

    // Reviews (Ulasan)
    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('admin.notifications.create');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('admin.notifications.store');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('admin.reports.csv');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.pdf');

    // CMS Banners
    Route::resource('banners', BannerController::class)->names('admin.banners');

    // CMS Pages
    Route::resource('pages', PageController::class)->names('admin.pages');
});


// ==========================================
// 1. RUTE PUBLIK (Dapat diakses tanpa login)
// ==========================================

// Rute halaman utama dan pencarian produk
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/search/live', [CustomerProductController::class, 'liveSearch'])->name('api.search.live');
Route::get('/products', [CustomerProductController::class, 'index'])->name('product.index');
Route::get('/product/{slug}', [CustomerProductController::class, 'show'])->name('product.detail');

// Rute informasi statis (Biasanya dipanggil dari Footer)
Route::get('/store_location', function () { 
    $page = \App\Models\StaticPage::where('slug', 'store_location')->where('is_active', true)->first();
    return view('customer.pages.store_location', compact('page')); 
})->name('store_location');

Route::get('/contact_store', function () { 
    $page = \App\Models\StaticPage::where('slug', 'contact_store')->where('is_active', true)->first();
    return view('customer.pages.contact_store', compact('page')); 
})->name('contact_store');

Route::get('/kebijakan-keamanan-data', function () { 
    $page = \App\Models\StaticPage::where('slug', 'kebijakan-keamanan-data')->where('is_active', true)->first();
    return view('customer.pages.privacy', compact('page')); 
})->name('privacy');

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

// Rute Proxy API untuk data wilayah (Emsifa) guna menghindari CORS policy block
Route::get('/api/wilayah/provinces', function () {
    $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
    return response()->json($response->json(), $response->status());
});

Route::get('/api/wilayah/regencies/{province_id}', function ($province_id) {
    $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/regencies/{$province_id}.json");
    return response()->json($response->json(), $response->status());
});

Route::get('/api/wilayah/districts/{city_id}', function ($city_id) {
    $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/districts/{$city_id}.json");
    return response()->json($response->json(), $response->status());
});

Route::get('/api/wilayah/villages/{district_id}', function ($district_id) {
    $response = Http::get("https://www.emsifa.com/api-wilayah-indonesia/api/villages/{$district_id}.json");
    return response()->json($response->json(), $response->status());
});



// ==========================================
// 2. RUTE WEBHOOK (Sistem ke Sistem)
// ==========================================

// Webhook Midtrans (Wajib ditaruh di luar middleware auth & tanpa CSRF agar Midtrans bisa masuk)
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// 🌟 Webhook Biteship (RUTE BARU)
Route::post('/biteship/webhook', [\App\Http\Controllers\Customer\BiteshipWebhookController::class, 'handleWebhook'])
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

    // Mengarah ke form reset_password
    Route::get('/password_edit', function () { 
        return view('customer.pages.auth.reset_password', ['user' => auth()->user()]); 
    })->name('password_edit');
    
    // Rute Penerima Data Ganti Password
    Route::patch('/password/update-profile', [ProfileController::class, 'updatePassword'])->name('password.update.profile');
    
    // Halaman Edit Profil
    Route::get('/profile_edit', function () { 
        return view('customer.pages.profile_edit', ['user' => auth()->user()]); 
    })->name('profile_edit');
    
    // Action untuk memproses update profil ke Database
    Route::patch('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update'); 
    
    // Halaman Edit Login
    Route::get('/login_edit', function () { 
        return view('customer.pages.login_edit', ['user' => auth()->user()]); 
    })->name('login_edit');


    // --- BAGIAN ALAMAT PENGIRIMAN ---
    Route::get('/address_form', [AddressController::class, 'create'])->name('address_form'); // Halaman Form Alamat
    Route::post('/address/store', [AddressController::class, 'store'])->name('address.store'); // Action Simpan Alamat
    Route::patch('/address/{id}/set-main', [AddressController::class, 'setMain'])->name('address.set_main'); // Action Set Alamat Utama
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy'); // Action Hapus Alamat
    Route::get('/address/create', [AddressController::class, 'create'])->name('address.create'); // Tambah
    Route::get('/address/{id}/edit', [AddressController::class, 'edit'])->name('address.edit'); // Edit
    Route::put('/address/{id}/update', [AddressController::class, 'update'])->name('address.update'); // Simpan Perubahan


    // --- BAGIAN PESANAN, TRACKING & AKSI PESANAN ---
    Route::get('/orders', [ProfileController::class, 'index'])->name('order'); // Mengarah ke Tab Pesanan di Halaman Profil
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.detail'); // Halaman Rincian Pesanan
    Route::get('/profile/order/{id}/track', [\App\Http\Controllers\Customer\ProfileController::class, 'trackOrderPage'])->name('order.track');
    Route::get('/profile/order/{id}/tracking', [ProfileController::class, 'getTracking'])->name('order.tracking'); // Endpoint AJAX Cek Resi
    Route::get('/profile/order/{id}/invoice', [ProfileController::class, 'printInvoice'])->name('order.invoice'); // Endpoint Download PDF Invoice
    Route::post('/profile/order/{id}/cancel-manual', [ProfileController::class, 'cancelOrderManual'])->name('profile.order.cancel');
    
    // 🌟 RUTE BARU UNTUK FITUR TOMBOL AKSI 🌟
    Route::post('/profile/order/{id}/complete', [ProfileController::class, 'completeOrder'])->name('order.complete');
    Route::get('/profile/order/{id}/buy-again', [ProfileController::class, 'buyAgain'])->name('order.buy-again');
    Route::post('/profile/order/review', [ProfileController::class, 'storeReview'])->name('order.review');


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
    Route::get('/order_success', function (Request $request) {
        $orderId = $request->query('order_id');
        if ($orderId) {
            $order = \App\Models\Order::where('user_id', auth()->id())
                ->where('invoice_number', $orderId)
                ->first();
        } else {
            $order = \App\Models\Order::where('user_id', auth()->id())->latest()->first();
        }
        return view('customer.pages.order_success', compact('order'));
    })->name('order_success');


    // --- LAIN-LAIN ---
    Route::get('/notification', [ProfileController::class, 'notifications'])->name('notification');
    Route::get('/chatbot', function () { return view('customer.pages.chatbot'); })->name('chatbot');

}); // <-- PENUTUP BLOK AUTH

// Memuat rute autentikasi bawaan Laravel (Login, Register, Reset Password)
require __DIR__.'/auth.php';