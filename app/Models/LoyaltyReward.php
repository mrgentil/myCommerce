<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    protected $fillable = [
        'name',
        'description',
        'points_required',
        'reward_type',
        'reward_value',
        'product_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reward_value' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get reward type label
     */
    public function getRewardTypeLabelAttribute()
    {
        return match($this->reward_type) {
            'discount_percent' => $this->reward_value . '% de réduction',
            'discount_fixed' => $this->reward_value . '€ de réduction',
            'free_shipping' => 'Livraison gratuite',
            'product' => 'Produit gratuit',
            default => $this->reward_type,
        };
    }
}
