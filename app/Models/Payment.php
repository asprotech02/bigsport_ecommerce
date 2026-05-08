<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // 🌟 FIX: Tambahkan izin fillable agar tidak Error 500
    protected $fillable = [
        'order_id',
        'midtrans_transaction_id',
        'payment_type',
        'payment_status',
        'bank_name',
        'gross_amount',
    ];

    public function order() { return $this->belongsTo(Order::class); }
}