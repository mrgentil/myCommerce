<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'name', 'slug', 'logo', 'description', 'status', 'banner', 'address', 'phone',
        'hero_title', 'hero_subtitle', 'hero_button_text', 'hero_button_link', 'hero_background', 'hero_text_color',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            $shop->slug = Str::slug($shop->name);
        });
    }

    /**
     * Get the vendor that owns the shop.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get all products in this shop.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get shop followers
     */
    public function followers()
    {
        return $this->hasMany(ShopFollower::class);
    }

    /**
     * Get followers as customers
     */
    public function followerCustomers()
    {
        return $this->belongsToMany(Customer::class, 'shop_followers');
    }

    /**
     * Check if shop is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
