<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    // 🌟 Tambahkan fillable baru
    protected $fillable = ['product_id', 'size', 'color', 'stock', 'reserved_stock', 'base_price', 'discount_price'];

    // 🌟 FIX: Kasih tau Laravel buat selalu bawa kolom virtual ini kalau diubah ke JSON (buat JavaScript)
    protected $appends = ['available_stock'];

    // 🌟 LOGIKA ENTERPRISE: Bikin atribut virtual untuk stok yang benar-benar bisa dibeli
    public function getAvailableStockAttribute()
    {
        // Pastikan sisa stok tidak pernah minus di tampilan
        $available = $this->stock - $this->reserved_stock;
        return $available > 0 ? $available : 0;
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function cartItems() { return $this->hasMany(Cart::class); }
}