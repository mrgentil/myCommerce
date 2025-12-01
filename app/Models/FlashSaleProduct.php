<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSaleProduct extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'product_variant_id',
        'sale_price',
        'quantity_limit',
        'quantity_sold',
        'per_customer_limit',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
    ];

    public function flashSale()
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Check if still available
     */
    public function isAvailable()
    {
        if (!$this->quantity_limit) {
            return true;
        }
        return $this->quantity_sold < $this->quantity_limit;
    }

    /**
     * Get remaining quantity
     */
    public function getRemainingAttribute()
    {
        if (!$this->quantity_limit) {
            return null;
        }
        return max(0, $this->quantity_limit - $this->quantity_sold);
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        $originalPrice = $this->variant?->price ?? $this->product?->primaryVariant?->price ?? 0;
        if ($originalPrice <= 0) return 0;
        
        return round((($originalPrice - $this->sale_price) / $originalPrice) * 100);
    }

    /**
     * Get sold percentage
     */
    public function getSoldPercentageAttribute()
    {
        if (!$this->quantity_limit) {
            return 0;
        }
        return min(100, round(($this->quantity_sold / $this->quantity_limit) * 100));
    }
}
