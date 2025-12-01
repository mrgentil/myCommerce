<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendor extends Authenticatable
{
    use Notifiable;

    protected $guard = 'vendor';

    protected $fillable = ['name', 'email', 'password', 'phone', 'status', 'profile_image', 'commission_rate'];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'commission_rate' => 'decimal:2',
    ];

    /**
     * Get the shop owned by the vendor.
     */
    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    /**
     * Get all products of the vendor.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check if vendor is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if vendor is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
