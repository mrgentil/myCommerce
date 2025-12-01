<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'orders';

    // Define the fields that can be mass-assigned
    protected $fillable = [
        'customer_id',
        'vendor_id',
        'guest_email',
        'order_date',
        'status',
        'total_amount',
        'total_price',
        'shipping_address',
        'billing_address',
        'payment_method',
        'payment_status',
        'shipping_method',
        'tracking_number',
        'carrier',
        'estimated_delivery',
        'shipped_at',
        'delivered_at',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'coupon_code',
        'notes',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'estimated_delivery' => 'date',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Define the relationship with the Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class)->orderBy('created_at', 'desc');
    }

    public function latestTracking()
    {
        return $this->hasOne(OrderTracking::class)->latestOfMany();
    }

    /**
     * Add tracking event
     */
    public function addTrackingEvent($status, $title, $description = null, $location = null)
    {
        return OrderTracking::addEvent($this->id, $status, $title, $description, $location);
    }

    /**
     * Update order status with tracking
     */
    public function updateStatusWithTracking($status, $title, $description = null)
    {
        $this->update(['status' => $status]);
        $this->addTrackingEvent($status, $title, $description);

        // Update specific timestamps
        if ($status === 'shipped') {
            $this->update(['shipped_at' => now()]);
        } elseif ($status === 'delivered') {
            $this->update(['delivered_at' => now()]);
        }
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        return match($this->status) {
            'pending' => 10,
            'confirmed' => 25,
            'processing' => 40,
            'shipped' => 60,
            'in_transit' => 75,
            'out_for_delivery' => 90,
            'delivered' => 100,
            'cancelled' => 0,
            default => 0,
        };
    }

    /**
     * Get status label in French
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'processing' => 'En préparation',
            'shipped' => 'Expédiée',
            'in_transit' => 'En transit',
            'out_for_delivery' => 'En livraison',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
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
}
