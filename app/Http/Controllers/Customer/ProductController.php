<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tangkap parameter dasar
        $type = $request->query('type');               
        $search = $request->query('search');
        $gender = $request->query('gender');           
        $category = $request->query('category');       
        $subcategory = $request->query('subcategory'); 

        $sort = $request->query('sort', 'rekomendasi');
        $filterBrand = (array) $request->query('brand', []); 
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $filterSize = (array) $request->query('size', []);  

        // 2. SINKRONISASI: Gabungkan singular (Navbar) ke dalam array (Filter Offcanvas)
        $filterGen = (array) $request->query('gen', []);
        if ($gender && !in_array($gender, $filterGen)) {
            $filterGen[] = $gender; // Masukkan gender dari URL ke dalam array filter
        }

        $filterCat = (array) $request->query('cat', []);
        if ($category && !in_array($category, $filterCat)) {
            $filterCat[] = $category; // Masukkan category dari URL ke dalam array filter
        }

        // 3. Query Dasar
        $query = Product::select('products.*')
                    ->with(['brand', 'category', 'skus', 'images' => function($q) {
                        $q->where('is_primary', true);
                    }])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews')
                    ->withSum('skus', 'stock'); // <-- SUNTIKAN 1: Hitung total stok

        // 4. LOGIKA JUDUL BREADCRUMB (Cerdas membaca array filter)
        $title = 'SEMUA PRODUK';
        if ($type == 'sale') {
            $query->whereNotNull('discount_price')->where('base_price', '>', 0);
            $title = 'SALE';
        } elseif ($type == 'new') {
            $query->where('created_at', '>=', Carbon::now()->subMonths(6));
            $title = 'PRODUK BARU';
        } elseif ($type == 'featured') {
            $query->where('is_featured', true)
                  ->whereHas('skus', function($q) {
                      $q->where('stock', '>', 0); 
                  })
                  ->addSelect(['total_sold' => DB::table('order_items')
                      ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
                      ->whereColumn('product_skus.product_id', 'products.id')
                      ->selectRaw('IFNULL(SUM(order_items.quantity), 0)')
                  ]);
            $title = 'EKSKLUSIF';
        }

        // Jika hanya 1 gender yang difilter, jadikan judul
        $displayGender = $gender ?: (count($filterGen) == 1 ? $filterGen[0] : null);
        if ($displayGender) {
            $title = ($title == 'SEMUA PRODUK') ? 'KATEGORI ' . strtoupper($displayGender) : $title . ' ' . strtoupper($displayGender);
        }

        // Jika hanya 1 kategori yang difilter, jadikan judul
        $displayCategory = $category ?: (count($filterCat) == 1 ? $filterCat[0] : null);
        if ($displayCategory) {
            $title .= ' - ' . strtoupper($displayCategory);
        }

        if ($subcategory) {
            $query->whereHas('subcategory', function($q) use ($subcategory) {
                $q->where('name', $subcategory);
            });
            $title .= ' - ' . strtoupper($subcategory);
        }

        // Ubah judul jika sedang mencari produk
        if ($search) {
            $title = 'HASIL PENCARIAN: "' . strtoupper($search) . '"';
        }

        // 5. TERAPKAN FILTER
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%') 
                  ->orWhere('description', 'LIKE', '%' . $search . '%') 
                  ->orWhereHas('brand', function($b) use ($search) {
                      $b->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('category', function($c) use ($search) {
                      $c->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        if (!empty($filterGen)) {
            $query->whereIn('gender', $filterGen);
        }

        if (!empty($filterCat)) {
            $query->whereHas('category', function($q) use ($filterCat) {
                $q->whereIn('name', $filterCat);
            });
        }

        if (!empty($filterBrand)) {
            $query->whereHas('brand', function($q) use ($filterBrand) {
                $q->whereIn('name', $filterBrand);
            });
        }

        if ($minPrice) {
            $query->whereRaw('COALESCE(discount_price, base_price) >= ?', [$minPrice]);
        }
        if ($maxPrice) {
            $query->whereRaw('COALESCE(discount_price, base_price) <= ?', [$maxPrice]);
        }

        if (!empty($filterSize)) {
            $query->whereHas('skus', function($q) use ($filterSize) {
                $q->whereIn('size', $filterSize)->where('stock', '>', 0);
            });
        }

        // 6. SORTING (PENGURUTAN)
        $query->orderByRaw('CASE WHEN skus_sum_stock > 0 THEN 1 ELSE 0 END DESC');
        
        if ($sort == 'terbaru') {
            $query->latest();
        } elseif ($sort == 'harga_tertinggi') {
            $query->orderByRaw('COALESCE(discount_price, base_price) DESC');
        } elseif ($sort == 'harga_terendah') {
            $query->orderByRaw('COALESCE(discount_price, base_price) ASC');
        } elseif ($sort == 'diskon') {
            $query->whereNotNull('discount_price')
                ->whereColumn('discount_price', '<', 'base_price');
            $query->orderByRaw('((base_price - discount_price) / base_price) * 100 DESC');
        } elseif ($type == 'featured') {
            $query->orderBy('total_sold', 'DESC');
        } else {
            $query->latest();
        }

        $products = $query->paginate(32)->withQueryString();

        return view('customer.pages.product', compact('products', 'title'));
    }

    public function show($slug)
    {
        // Panggil produk beserta relasi tabel yang dibutuhkan
        $product = Product::with([
                'brand', 
                'category', 
                'subcategory', 
                'images', 
                'skus', 
                'reviews.user' 
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('slug', $slug)
            ->firstOrFail();

        // Ambil gambar utama
        $primaryImage = $product->images->where('is_primary', true)->first();

        // Hitung total terjual
        $totalSold = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('product_skus', 'order_items.product_sku_id', '=', 'product_skus.id')
            ->where('product_skus.product_id', $product->id)
            ->sum('quantity');

        // REVISI 2: Ambil Produk Rekomendasi berdasarkan kategori yang sama (Kecuali produk ini sendiri)
        $recommendedProducts = Product::with(['brand', 'category', 'images' => function($q) {
                $q->where('is_primary', true);
            }])
            ->withAvg('reviews', 'rating')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Jangan tampilkan produk yang sedang dilihat
            ->inRandomOrder() // Acak biar dinamis, atau bisa pakai ->latest()
            ->take(10) // Tampilkan 4 rekomendasi
            ->get();

        return view('customer.pages.detail_product', compact('product', 'primaryImage', 'totalSold', 'recommendedProducts'));
    }
}