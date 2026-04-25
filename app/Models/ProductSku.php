<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    public function product() { return $this->belongsTo(Product::class); }
    // Digunakan saat checkout/keranjang
    public function cartItems() { return $this->hasMany(Cart::class); }
}
