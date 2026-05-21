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
            $filterGen[] = $gender; 
        }

        $filterCat = (array) $request->query('cat', []);
        if ($category && !in_array($category, $filterCat)) {
            $filterCat[] = $category; 
        }

        // 3. Query Dasar (Eloquent)
        $query = Product::select('products.*')
                    ->with(['brand', 'category', 'skus', 'images' => function($q) {
                        $q->where('is_primary', true);
                    }])
                    ->withAvg('reviews', 'rating')
                    ->withCount('reviews')
                    ->withSum('skus', 'stock'); 

        // 4. LOGIKA JUDUL BREADCRUMB
        $title = 'SEMUA PRODUK';
        
        if ($type == 'sale') {
            $query->whereHas('skus', function($q) {
                $q->whereNotNull('discount_price')->where('base_price', '>', 0);
            });
            $title = 'FLASH SALE 🔥';
            $sort = 'diskon'; 

        } elseif ($type == 'new') {
            $query->where('created_at', '>=', Carbon::now()->subMonths(6));
            $title = 'PRODUK BARU 🆕';
            
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
            $title = 'PRODUK EKSKLUSIF ⚡';
            $sort = 'featured';
        }

        $displayGender = $gender ?: (count($filterGen) == 1 ? $filterGen[0] : null);
        if ($displayGender) {
            $title = ($title == 'SEMUA PRODUK') ? 'KATEGORI ' . strtoupper($displayGender) : $title . ' ' . strtoupper($displayGender);
        }

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

        if ($search) {
            $title = 'HASIL PENCARIAN: "' . strtoupper($search) . '"';
        }

        // 🌟 5. TERAPKAN PENCARIAN PINTAR (TYPO-TOLERANCE SCOUT) 🌟
        if (!empty($search)) {
            // Ambil maksimal 1000 ID produk yang relevan dari mesin Meilisearch
            $searchResultIds = Product::search($search)->take(1000)->keys()->toArray();

            if (empty($searchResultIds)) {
                // Jika Meilisearch tidak menemukan apa-apa, kosongkan hasil query DB dengan aman
                $query->whereNull('products.id');
            } else {
                // Lempar ID dari Meilisearch ke dalam query Eloquent kita
                $query->whereIn('products.id', $searchResultIds);
                
                // Jika user tidak memilih sorting khusus, urutkan berdasarkan tingkat kecocokan Meilisearch
                if ($sort == 'rekomendasi') {
                    $idsOrdered = implode(',', $searchResultIds);
                    $query->orderByRaw("FIELD(products.id, $idsOrdered)");
                }
            }
        }

        // 6. TERAPKAN SISA FILTER
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

        if (!empty($filterSize)) {
            $query->whereHas('skus', function($q) use ($filterSize) {
                $q->whereIn('size', $filterSize)->where('stock', '>', 0);
            });
        }

        if ($minPrice || $maxPrice) {
            $query->whereHas('skus', function($q) use ($minPrice, $maxPrice) {
                if ($minPrice) {
                    $q->whereRaw('COALESCE(discount_price, base_price) >= ?', [$minPrice]);
                }
                if ($maxPrice) {
                    $q->whereRaw('COALESCE(discount_price, base_price) <= ?', [$maxPrice]);
                }
            });
        }

        // 7. SORTING (PENGURUTAN)
        // Prioritaskan barang yang masih ada stoknya di atas
        $query->orderByRaw('CASE WHEN skus_sum_stock > 0 THEN 1 ELSE 0 END DESC');
        
        // Jangan timpa sorting Meilisearch jika user memilih 'rekomendasi' dan sedang mencari sesuatu
        if (!($sort == 'rekomendasi' && !empty($search))) {
            if ($sort == 'terbaru') {
                $query->latest();
            } elseif ($sort == 'harga_tertinggi') {
                $query->addSelect(['max_price_sort' => DB::table('product_skus')
                    ->whereColumn('product_id', 'products.id')
                    ->selectRaw('MAX(COALESCE(discount_price, base_price))')
                ])->orderBy('max_price_sort', 'DESC');

            } elseif ($sort == 'harga_terendah') {
                $query->addSelect(['min_price_sort' => DB::table('product_skus')
                    ->whereColumn('product_id', 'products.id')
                    ->selectRaw('MIN(COALESCE(discount_price, base_price))')
                ])->orderBy('min_price_sort', 'ASC');

            } elseif ($sort == 'diskon') {
                $query->addSelect(['max_discount_pct' => DB::table('product_skus')
                    ->whereColumn('product_id', 'products.id')
                    ->whereNotNull('discount_price')
                    ->selectRaw('MAX(((base_price - discount_price) / base_price) * 100)')
                ])->orderBy('max_discount_pct', 'DESC');

            } elseif ($sort == 'featured' || $type == 'featured') {
                $query->orderBy('total_sold', 'DESC')->latest();
            } else {
                $query->latest();
            }
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
                'reviews.user',
                'reviews.images' 
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

        // Ambil Produk Rekomendasi berdasarkan kategori yang sama
        $recommendedProducts = Product::with(['brand', 'category', 'skus', 'images' => function($q) {
                $q->where('is_primary', true);
            }])
            ->withAvg('reviews', 'rating')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) 
            ->inRandomOrder() 
            ->take(10) 
            ->get();

        return view('customer.pages.detail_product', compact('product', 'primaryImage', 'totalSold', 'recommendedProducts'));
    }
}