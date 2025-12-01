<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'title',
        'description',
        'location',
        'tracking_number',
        'carrier',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get status icon
     */
    public function getIconAttribute()
    {
        return match($this->status) {
            'pending' => 'bi-clock',
            'confirmed' => 'bi-check-circle',
            'processing' => 'bi-gear',
            'shipped' => 'bi-truck',
            'in_transit' => 'bi-airplane',
            'out_for_delivery' => 'bi-bicycle',
            'delivered' => 'bi-house-check',
            'cancelled' => 'bi-x-circle',
            default => 'bi-circle',
        };
    }

    /**
     * Get status color
     */
    public function getColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'info',
            'in_transit' => 'primary',
            'out_for_delivery' => 'success',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Create tracking event for order
     */
    public static function addEvent($orderId, $status, $title, $description = null, $location = null)
    {
        return self::create([
            'order_id' => $orderId,
            'status' => $status,
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'occurred_at' => now(),
        ]);
    }
}
