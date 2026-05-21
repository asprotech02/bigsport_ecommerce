<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'promo_id', // 🌟 Wajib ada untuk relasi diskon
        'invoice_number',
        'shipping_address',
        'total_product_price',
        'total_shipping_cost',
        'discount_amount',
        'grand_total',
        'snap_token',
        'payment_status',
        'payment_type',
        'status',
        'cancel_reason', // 🌟 WAJIB TAMBAHKAN BARIS INI!
        // 🌟 biteship_order_id, waybill_id, courier_company, courier_type DIHAPUS
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function address() { return $this->belongsTo(Address::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function shippingDetail() { return $this->hasOne(ShippingDetail::class); }
    
    // 🌟 Relasi Baru
    public function promo() { return $this->belongsTo(Promo::class); }
}