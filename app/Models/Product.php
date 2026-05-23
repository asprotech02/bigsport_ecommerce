<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // 🌟 INI OBATNYA BRO!
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable; // 🌟 TAMBAHKAN INI

class Product extends Model
{
    use HasFactory, Searchable; // 🌟 TAMBAHKAN Searchable DI SINI

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'gender',
        'name',
        'slug',
        'description',
        'weight_gram',
        'is_featured'
    ]; // 🌟 base_price dan discount_price DIHAPUS[cite: 2]

    public function category() { return $this->belongsTo(Category::class); }
    public function subcategory() { return $this->belongsTo(Subcategory::class); }
    public function brand() { return $this->belongsTo(Brand::class); }
    public function images() { return $this->hasMany(ProductImage::class); }
    public function skus() { return $this->hasMany(ProductSku::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    
        // 🌟 PASTE METHOD BARU INI DI BAWAH RELASI
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category_name' => $this->category ? $this->category->name : '',
            'brand_name' => $this->brand ? $this->brand->name : '',
        ];
    }

    public function getImageUrlAttribute()
    {
        $image = $this->images->firstWhere('is_primary', true);
        return $image
            ? asset('storage/' . $image->image_path)
            : 'https://placehold.co/600x600?text=No+Image';
    }

    public function getLowestPriceAttribute()
    {
        if ($this->skus->isEmpty()) {
            return 0;
        }
        return $this->skus->min(function ($sku) {
            return $sku->discount_price ?? $sku->base_price;
        });
    }

    public function getTotalStockAttribute()
    {
        return $this->skus->sum('stock');
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->skus->sum('stock') <= 0;
    }
}