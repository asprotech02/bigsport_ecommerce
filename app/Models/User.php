<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // <-- 1. Garis miring (//) sudah dihapus
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail 
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable; // <-- 2. Hanya dipanggil satu kali saja

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [     // <-- 3. Dijadikan satu array agar tidak saling timpa
        'name',
        'email',
        'password',
        'birthday',
        'gender',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELASI DATABASE ---
    public function addresses() { return $this->hasMany(Address::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function carts() { return $this->hasMany(Cart::class); }
    public function wishlists() { return $this->hasMany(Wishlist::class); }
}