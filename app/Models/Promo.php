<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'code',
        'type',
        'reward',
        'max_usage',
        'used_count',
        'min_order_amount',
        'is_active',
        'expires_at'
    ];

    // 🌟 Relasi Many-to-Many via pivot promo_user[cite: 2]
    public function users()
    {
        return $this->belongsToMany(User::class, 'promo_user')
                    ->withPivot('used_at')
                    ->withTimestamps();
    }
}