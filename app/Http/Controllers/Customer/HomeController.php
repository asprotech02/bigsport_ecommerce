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
                                    ->with(['brand', 'category', 'skus', 'images' => function($q) {
                                        $q->where('is_primary', true);
                                    }])
                                    ->whereHas('skus', function($q) {
                                        $q->whereNotNull('discount_price')->where('stock', '>', 0);
                                    })
                                    ->addSelect(['max_discount_pct' => DB::table('product_skus')
                                        ->whereColumn('product_id', 'products.id')
                                        ->whereNotNull('discount_price')
                                        ->selectRaw('MAX(((base_price - discount_price) / base_price) * 100)')
                                    ])
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews')
                                    ->orderBy('max_discount_pct', 'DESC') 
                                    ->latest()
                                    ->take(8) 
                                    ->get();

        // 2. PRODUK PILIHAN (Featured, Stok > 0, Terlaris, dan Terbaru)
        $featuredProducts = Product::select('products.*') // Wajib ada jika pakai addSelect
                                    ->with(['brand', 'category', 'skus', 'images' => function($query) {
                                        $query->where('is_primary', true);
                                    }])
                                    ->where('is_featured', true) // Syarat: Produk Pilihan
                                    ->whereHas('skus', function($query) {
                                        $query->where('stock', '>', 0); // Syarat: Stok > 0
                                    })
                                    ->withAvg('reviews', 'rating')
                                    ->withCount('reviews') 
                                    // LOGIKA TERLARIS: Hitung total quantity terjual dari order_items
                                    ->addSelect(['total_sold' => DB::table('order_items')
                                        ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
                                        ->whereColumn('product_skus.product_id', 'products.id')
                                        ->selectRaw('IFNULL(SUM(order_items.quantity), 0)')
                                    ])
                                    ->orderBy('total_sold', 'DESC') // Urutkan 1: Paling banyak terjual
                                    ->latest() // Urutkan 2: Paling baru ditambahkan (Secondary sort)
                                    ->take(8) 
                                    ->get();

        return view('customer.pages.home', [
            'discountProducts' => $discountProducts,
            'featuredProducts' => $featuredProducts,
            'gender' => 'Semua',
            'category' => 'Koleksi',
            'subcategory' => 'Terbaru'
        ]);
    }
}