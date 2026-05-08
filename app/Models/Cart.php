<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id', // 🌟 Wajib ditambahkan untuk fitur Guest Checkout[cite: 2]
        'product_sku_id',
        'quantity'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function productSku() { return $this->belongsTo(ProductSku::class); }
}