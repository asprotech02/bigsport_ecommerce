<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    // 🌟 Tambahkan fillable sesuai ERD v2.0
    protected $fillable = [
        'order_id',
        'biteship_order_id',
        'courier_company',
        'courier_type',
        'tracking_number',
        'cost',
    ];

    public function order() { return $this->belongsTo(Order::class); }
}