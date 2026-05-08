<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'birthday',
        'gender',
        'phone_number',
        'role', // Jika role dimasukkan pas registrasi
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses() { return $this->hasMany(Address::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function carts() { return $this->hasMany(Cart::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class); }
    
    // 🌟 Relasi ke promo yang sudah pernah dipakai
    public function usedPromos()
    {
        return $this->belongsToMany(Promo::class, 'promo_user')
                    ->withPivot('used_at')
                    ->withTimestamps();
    }
}