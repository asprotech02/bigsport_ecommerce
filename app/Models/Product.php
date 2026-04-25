<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category() { return $this->belongsTo(Category::class); }
    public function brand() { return $this->belongsTo(Brand::class); }

    public function images() { return $this->hasMany(ProductImage::class); }
    public function skus() { return $this->hasMany(ProductSku::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function getImageUrlAttribute()
    {
        $image = $this->images->firstWhere('is_primary', true);

        return $image
            ? asset('storage/' . $image->image_path)
            : 'https://placehold.co/600x600?text=No+Image';
    }
}
