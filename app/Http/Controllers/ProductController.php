<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. DYNAMIC FILTERING LOGIC
        $gender = strtoupper($request->query('gender', 'PRIA'));
        $category = strtoupper($request->query('category', 'SEPATU'));
        $subcategory = strtoupper($request->query('subcategory', 'SEPAK BOLA'));

        $products = [];

        // --- SKENARIO A: Pria -> Sepatu ---
        if ($gender == 'PRIA' && $category == 'SEPATU') {
            $products = [
                [
                    'id' => 1, 'brand' => 'ADIDAS', 'name' => 'ADISTAR CONTROL 5 UNISEX SNEAKERS', 'gender_type' => 'Unisex', 'color' => 'Abu-abu', 'price' => 1900000, 'rating' => 4, 'reviews' => 117, 
                    'image' => 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
                [
                    'id' => 2, 'brand' => 'NIKE', 'name' => 'MERCURIAL VAPOR 14 ELITE FG', 'gender_type' => 'Pria', 'color' => 'Merah', 'price' => 3500000, 'rating' => 5, 'reviews' => 342, 
                    'image' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
                [
                    'id' => 3, 'brand' => 'PUMA', 'name' => 'FUTURE Z 1.2 FG/AG MEN', 'gender_type' => 'Pria', 'color' => 'Putih', 'price' => 2800000, 'rating' => 4, 'reviews' => 89, 
                    'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
                [
                    'id' => 4, 'brand' => 'ADIDAS', 'name' => 'SAMBA CLASSIC INDOOR', 'gender_type' => 'Unisex', 'color' => 'Hitam', 'price' => 1500000, 'rating' => 5, 'reviews' => 512, 
                    'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
            ];
        } 
        // --- SKENARIO B: Perempuan -> Pakaian ---
        elseif ($gender == 'PEREMPUAN' && $category == 'PAKAIAN') {
            $products = [
                [
                    'id' => 5, 'brand' => 'NIKE', 'name' => 'DRI-FIT WOMEN TRAINING T-SHIRT', 'gender_type' => 'Wanita', 'color' => 'Pink', 'price' => 250000, 'rating' => 5, 'reviews' => 204, 
                    'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
                [
                    'id' => 6, 'brand' => 'ADIDAS', 'name' => 'ORIGINALS TREFOIL TEE', 'gender_type' => 'Wanita', 'color' => 'Putih', 'price' => 450000, 'rating' => 4, 'reviews' => 156, 
                    'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
                [
                    'id' => 7, 'brand' => 'PUMA', 'name' => 'ESSENTIALS LOGO HEATHER TEE', 'gender_type' => 'Wanita', 'color' => 'Abu-abu', 'price' => 199000, 'rating' => 3, 'reviews' => 45, 
                    'image' => 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'
                ],
            ];
        }

        // 3. Melempar data ke View
        return view('pages.customer.product', compact('products', 'gender', 'category', 'subcategory'));
    }
}