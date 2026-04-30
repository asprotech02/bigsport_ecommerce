<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    // IZINKAN LARAVEL MENYIMPAN DATA KE KOLOM INI
    protected $fillable = [
        'user_id',
        'product_sku_id',
        'quantity'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}