<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user() { return $this->belongsTo(User::class); }
    public function address() { return $this->belongsTo(Address::class); }

    // Relasi ke item belanjaan
    public function items() { return $this->hasMany(OrderItem::class); }

    // Relasi 1-ke-1 untuk log API pihak ketiga
    public function payment() { return $this->hasOne(Payment::class); }
    public function shippingDetail() { return $this->hasOne(ShippingDetail::class); }
}
