<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'banner',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    /**
     * Scope for active flash sales
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /**
     * Scope for upcoming flash sales
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '>', now());
    }

    /**
     * Check if sale is currently active
     */
    public function isLive()
    {
        return $this->is_active 
            && $this->starts_at <= now() 
            && $this->ends_at >= now();
    }

    /**
     * Get time remaining
     */
    public function getTimeRemainingAttribute()
    {
        if (!$this->isLive()) {
            return null;
        }
        return $this->ends_at->diffForHumans(['parts' => 3]);
    }

    /**
     * Get seconds remaining (for countdown)
     */
    public function getSecondsRemainingAttribute()
    {
        if (!$this->isLive()) {
            return 0;
        }
        return now()->diffInSeconds($this->ends_at);
    }

    /**
     * Get current active flash sale
     */
    public static function getCurrentSale()
    {
        return self::active()->with('products.product.translation', 'products.product.thumbnail')->first();
    }
}
