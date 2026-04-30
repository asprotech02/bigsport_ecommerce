<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // 👇👇👇 INI YANG BIKIN ERROR KALAU HILANG 👇👇👇
    protected $fillable = [
        'user_id',
        'address_id',
        'promo_id',
        'invoice_number',
        'total_product_price',
        'total_shipping_cost',
        'discount_amount',
        'grand_total',
        'status',
        'payment_status',
        'payment_type',
        'courier_company',
        'courier_type',
        'snap_token',
    ];

    // Relasi-relasi lu tetap di bawah sini
    public function user() { return $this->belongsTo(User::class); }
    public function address() { return $this->belongsTo(Address::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function shippingDetail() { return $this->hasOne(ShippingDetail::class); }
}