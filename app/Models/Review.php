<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_item_id', // 🌟 Aturan Verified Buyer[cite: 2]
        'rating',
        'comment'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    
    // 🌟 Relasi ke order_items
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
}