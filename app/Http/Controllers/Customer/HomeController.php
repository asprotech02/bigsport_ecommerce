<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller; 
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // PENTING: Tambahkan ini untuk sub-query Terlaris
use Illuminate\Support\Facades\Cache; // 🌟 JANGAN LUPA IMPORT INI DI PALING ATAS

class HomeController extends Controller
{
    public function index()
    {

        $latestProducts = Cache::remember('home_latest_products', 3600, function () {
            return Product::with(['images', 'skus'])
                        ->latest()
                        ->take(8)
                        ->get();
        });

        // 1. PRODUK DISKON (Diskon Terbesar, STOK > 0, dan Terbaru)
        // 🌟 FIX: Cari diskon terbesar via relasi SKU menggunakan Sub-query[cite: 2]
        $discountProducts = Product::select('products.*')
                                    ->selectRaw('(select MAX(((base_price - discount_price) / base_price) * 100) from product_skus where product_id = products.id and discount_price is not null) as max_discount_pct')
                                    ->with(['brand', 'category', 'skus', 'images' => function($q) {
                                        $q->where('is_primary', true);
                                    }])
                                    ->whereHas('skus', function($q) {
                                        $q->whereNotNull('discount_price')->where('stock', '>', 0);
                                    })
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews')
                                    ->orderBy('max_discount_pct', 'DESC') 
                                    ->latest()
                                    ->take(8) 
                                    ->get();

        // 2. PRODUK PILIHAN (Featured, Stok > 0, Terlaris, dan Terbaru)
        $featuredProducts = Product::select('products.*') // Wajib ada jika pakai addSelect
                                    ->selectRaw('(select COALESCE(SUM(order_items.quantity), 0) from order_items join product_skus on order_items.product_sku_id = product_skus.id where product_skus.product_id = products.id) as total_sold')
                                    ->with(['brand', 'category', 'skus', 'images' => function($query) {
                                        $query->where('is_primary', true);
                                    }])
                                    ->where('is_featured', true) // Syarat: Produk Pilihan
                                    ->whereHas('skus', function($query) {
                                        $query->where('stock', '>', 0); // Syarat: Stok > 0
                                    })
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews') 
                                    ->orderBy('total_sold', 'DESC') // Urutkan 1: Paling banyak terjual
                                    ->latest() // Urutkan 2: Paling baru ditambahkan (Secondary sort)
                                    ->take(8) 
                                    ->get();

        // 3. BANNERS (Slider & Promo)
        $sliderBanners = \App\Models\Banner::where('type', 'slider')->where('is_active', true)->orderBy('order')->get();
        $promoBanners = \App\Models\Banner::where('type', 'promo')->where('is_active', true)->orderBy('order')->get();

        return view('customer.pages.home', [
            'discountProducts' => $discountProducts,
            'featuredProducts' => $featuredProducts,
            'sliderBanners' => $sliderBanners,
            'promoBanners' => $promoBanners,
            'gender' => 'Semua',
            'category' => 'Koleksi',
            'subcategory' => 'Terbaru'
        ]);
    }
}