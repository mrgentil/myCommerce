<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopFollower extends Model
{
    protected $fillable = [
        'shop_id',
        'customer_id',
        'notify_new_products',
        'notify_promotions',
    ];

    protected $casts = [
        'notify_new_products' => 'boolean',
        'notify_promotions' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if customer follows shop
     */
    public static function isFollowing($shopId, $customerId)
    {
        return self::where('shop_id', $shopId)
            ->where('customer_id', $customerId)
            ->exists();
    }

    /**
     * Toggle follow
     */
    public static function toggle($shopId, $customerId)
    {
        $existing = self::where('shop_id', $shopId)
            ->where('customer_id', $customerId)
            ->first();

        if ($existing) {
            $existing->delete();
            Shop::where('id', $shopId)->decrement('followers_count');
            return false; // unfollowed
        }

        self::create([
            'shop_id' => $shopId,
            'customer_id' => $customerId,
        ]);
        Shop::where('id', $shopId)->increment('followers_count');
        return true; // followed
    }
}
