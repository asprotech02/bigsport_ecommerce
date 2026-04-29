<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

// Import Model
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSku;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. USER ADMIN & CUSTOMER
        User::create([
            'name' => 'Admin Big Sport',
            'email' => 'admin@bigsport.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // 2. DATA MASTER (KATEGORI, SUBKATEGORI, MEREK)
        $kategoriData = [
            'Sepatu' => ['Sepak Bola', 'Basket', 'Volly', 'Casual', 'Sepatu Lari'],
            'Pakaian' => ['Kaos', 'Jersey', 'Hoodie', 'Celana'],
            'Aksesoris' => ['Tas', 'Topi', 'Kaos Kaki', 'Alat Olahraga']
        ];

        $merekData = ['Adidas', 'Nike', 'Puma', 'Ortuseight', 'Specs'];
        $genderData = ['Laki-laki', 'Perempuan', 'Anak-anak', 'Unisex'];
        
        $brandsDb = [];
        $catsDb = [];
        $subcatsDb = [];

        foreach ($merekData as $merek) {
            $brandsDb[] = Brand::create(['name' => $merek, 'slug' => Str::slug($merek)]);
        }

        foreach ($kategoriData as $catName => $subCats) {
            $category = Category::create(['name' => $catName, 'slug' => Str::slug($catName)]);
            $catsDb[$catName] = $category;

            foreach ($subCats as $subName) {
                $subcatsDb[$catName][] = Subcategory::create([
                    'category_id' => $category->id, 
                    'name' => $subName, 
                    'slug' => Str::slug($subName)
                ]);
            }
        }

        // 3. GENERATOR 150 PRODUK DUMMY
        for ($i = 1; $i <= 150; $i++) {
            $randomCatName = array_rand($kategoriData);
            $kategori = $catsDb[$randomCatName];
            $subkategori = $faker->randomElement($subcatsDb[$randomCatName]);
            $merek = $faker->randomElement($brandsDb);
            $gender = $faker->randomElement($genderData);

            $namaProduk = $merek->name . ' ' . $subkategori->name . ' ' . ucfirst($faker->word);
            $basePrice = $faker->numberBetween(15, 250) * 10000; 
            
            // Logika Diskon (Agar sorting SR-02 bekerja dengan baik)
            $isDiscount = $faker->boolean(40); // 40% peluang diskon
            $discountPrice = null;
            if ($isDiscount) {
                $persenPotongan = $faker->randomElement([0.1, 0.2, 0.3, 0.5]); // 10% s/d 50%
                $discountPrice = $basePrice - ($basePrice * $persenPotongan);
            }

            $product = Product::create([
                'category_id'    => $kategori->id,
                'subcategory_id' => $subkategori->id,
                'brand_id'       => $merek->id,
                'gender'         => $gender,
                'name'           => $namaProduk,
                'slug'           => Str::slug($namaProduk . '-' . $i),
                'description'    => $faker->paragraph(3),
                'base_price'     => $basePrice,
                'discount_price' => $discountPrice,
                'is_featured'    => $faker->boolean(20),
                'weight_gram'    => $faker->numberBetween(200, 1000),
                'created_at'     => $faker->dateTimeBetween('-3 months', 'now'),
            ]);

            ProductImage::create([
                'product_id' => $product->id, 
                'image_path' => 'products/pegasus-40.jpg', 
                'is_primary' => true
            ]);

            // 4. LOGIKA UKURAN PINTAR (Disesuaikan dengan kategori)
            if ($randomCatName == 'Pakaian') {
                $availableSizes = ['S', 'M', 'L', 'XL', 'XXL'];
            } elseif ($randomCatName == 'Sepatu') {
                $availableSizes = ['38', '39', '40', '41', '42', '43', '44'];
            } else {
                $availableSizes = ['All Size'];
            }

            // Ambil 1 sampai 4 ukuran secara acak dari daftar yang sesuai
            $numSizes = ($randomCatName == 'Aksesoris') ? 1 : $faker->numberBetween(1, 4);
            $chosenSizes = $faker->randomElements($availableSizes, $numSizes);

            foreach ($chosenSizes as $size) {
                ProductSku::create([
                    'product_id' => $product->id, 
                    'size'       => $size, 
                    'stock'      => $faker->boolean(90) ? $faker->numberBetween(5, 50) : 0 
                ]);
            }
        }
    }
}