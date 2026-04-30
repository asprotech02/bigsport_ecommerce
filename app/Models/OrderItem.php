<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_sku_id',
        'product_name',
        'product_size',
        'price_at_purchase',
        'quantity',
    ];

    public function order() { 
        return $this->belongsTo(Order::class); 
    }
    
    // Relasi ke SKU
    public function sku() { 
        return $this->belongsTo(ProductSku::class, 'product_sku_id'); 
    }
}