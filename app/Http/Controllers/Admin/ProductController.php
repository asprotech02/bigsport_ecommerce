<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with([
            'category',
            'subcategory',
            'brand',
            'images',
            'skus'
        ])->latest()->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $brands = Brand::all();

        return view('admin.products.create', compact(
            'categories',
            'subcategories',
            'brands'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required',
            'gender' => 'required',
            'description' => 'nullable',
            'weight_gram' => 'required|numeric|min:1',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'skus' => 'required|array|min:1',
            'skus.*.size' => 'required',
            'skus.*.color' => 'required',
            'skus.*.base_price' => 'required|numeric|min:0',
            'skus.*.discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/skus\.(\d+)\.discount_price/', $attribute, $matches);
                    if (isset($matches[1])) {
                        $index = $matches[1];
                        $basePrice = $request->input("skus.{$index}.base_price");
                        if ($basePrice !== null && $value > $basePrice) {
                            $fail('Harga diskon harus lebih kecil atau sama dengan harga dasar.');
                        }
                    }
                }
            ],
            'skus.*.stock' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan data utama produk
            $product = Product::create([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'brand_id' => $request->brand_id,
                'gender' => $request->gender,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description ?? '',
                'weight_gram' => $request->weight_gram,
                'is_featured' => $request->is_featured ? 1 : 0,
            ]);

            // 2. Upload dan Simpan banyak Gambar sekaligus
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $primaryIndex = intval($request->input('primary_image_index', 0));

                foreach ($files as $index => $file) {
                    $path = $file->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => ($index === $primaryIndex) ? 1 : 0,
                    ]);
                }
            }

            // 3. Simpan data semua varian SKU
            foreach ($request->skus as $skuData) {
                ProductSku::create([
                    'product_id' => $product->id,
                    'size' => $skuData['size'],
                    'color' => $skuData['color'],
                    'base_price' => $skuData['base_price'],
                    'discount_price' => $skuData['discount_price'] ?? null,
                    'stock' => $skuData['stock'],
                    'reserved_stock' => 0,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan produk: ' . $e->getMessage()]);
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $brands = Brand::all();

        // Load relations explicitly
        $product->load(['images', 'skus']);

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'subcategories',
            'brands'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'brand_id' => 'required',
            'gender' => 'required',
            'description' => 'nullable',
            'weight_gram' => 'required|numeric|min:1',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'skus' => 'required|array|min:1',
            'skus.*.id' => 'nullable|exists:product_skus,id',
            'skus.*.size' => 'required',
            'skus.*.color' => 'required',
            'skus.*.base_price' => 'required|numeric|min:0',
            'skus.*.discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    preg_match('/skus\.(\d+)\.discount_price/', $attribute, $matches);
                    if (isset($matches[1])) {
                        $index = $matches[1];
                        $basePrice = $request->input("skus.{$index}.base_price");
                        if ($basePrice !== null && $value > $basePrice) {
                            $fail('Harga diskon harus lebih kecil atau sama dengan harga dasar.');
                        }
                    }
                }
            ],
            'skus.*.stock' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update data utama produk
            $product->update([
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'brand_id' => $request->brand_id,
                'gender' => $request->gender,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description ?? '',
                'weight_gram' => $request->weight_gram,
                'is_featured' => $request->is_featured ? 1 : 0,
            ]);

            // 2. Hapus gambar lama yang dicentang/ditandai hapus
            if ($request->filled('deleted_image_ids')) {
                $deletedIds = explode(',', $request->input('deleted_image_ids'));
                $imagesToDelete = ProductImage::whereIn('id', $deletedIds)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    Storage::disk('public')->delete($img->image_path);
                    $img->delete();
                }
            }

            // 3. Upload Gambar baru
            $newImageIds = [];
            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $index => $file) {
                    $path = $file->store('products', 'public');
                    $newImg = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => 0,
                    ]);
                    $newImageIds[$index] = $newImg->id;
                }
            }

            // 4. Kelola penentuan Gambar Utama (Primary)
            $primaryImageId = $request->input('primary_image_id');
            $primaryNewImageIndex = $request->input('primary_new_image_index');

            if ($primaryNewImageIndex !== null && isset($newImageIds[intval($primaryNewImageIndex)])) {
                // Gambar Utama diset ke gambar baru yang baru saja diupload
                ProductImage::where('product_id', $product->id)->update(['is_primary' => 0]);
                ProductImage::where('id', $newImageIds[intval($primaryNewImageIndex)])->update(['is_primary' => 1]);
            } elseif ($primaryImageId) {
                // Gambar Utama diset ke gambar lama
                ProductImage::where('product_id', $product->id)->update(['is_primary' => 0]);
                ProductImage::where('id', $primaryImageId)->update(['is_primary' => 1]);
            } else {
                // Fallback: pastikan ada minimal satu gambar utama
                $hasPrimary = ProductImage::where('product_id', $product->id)
                    ->where('is_primary', 1)
                    ->exists();

                if (!$hasPrimary) {
                    $firstImg = ProductImage::where('product_id', $product->id)->first();
                    if ($firstImg) {
                        $firstImg->update(['is_primary' => 1]);
                    }
                }
            }

            // 5. Hapus SKU lama yang dihapus dari form
            if ($request->filled('deleted_sku_ids')) {
                $deletedSkuIds = explode(',', $request->input('deleted_sku_ids'));
                ProductSku::whereIn('id', $deletedSkuIds)
                    ->where('product_id', $product->id)
                    ->delete();
            }

            // 6. Update secara bedah (surgical) SKU yang lama & Tambahkan SKU baru
            foreach ($request->skus as $skuData) {
                if (isset($skuData['id']) && !empty($skuData['id'])) {
                    // Update SKU Lama
                    $sku = ProductSku::where('id', $skuData['id'])
                        ->where('product_id', $product->id)
                        ->first();

                    if ($sku) {
                        $sku->update([
                            'size' => $skuData['size'],
                            'color' => $skuData['color'],
                            'base_price' => $skuData['base_price'],
                            'discount_price' => $skuData['discount_price'] ?? null,
                            'stock' => $skuData['stock'],
                        ]);
                    }
                } else {
                    // Buat SKU Baru
                    ProductSku::create([
                        'product_id' => $product->id,
                        'size' => $skuData['size'],
                        'color' => $skuData['color'],
                        'base_price' => $skuData['base_price'],
                        'discount_price' => $skuData['discount_price'] ?? null,
                        'stock' => $skuData['stock'],
                        'reserved_stock' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mengupdate produk: ' . $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Hapus file fisik dari storage
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image_path);
            }

            // Hapus relasi di database terlebih dahulu secara eksplisit
            $product->images()->delete();
            $product->skus()->delete();
            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }
}