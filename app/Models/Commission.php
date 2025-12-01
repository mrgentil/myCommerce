<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'order_id',
        'vendor_id',
        'order_amount',
        'commission_rate',
        'commission_amount',
        'vendor_amount',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vendor_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the order associated with the commission.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vendor associated with the commission.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Calculate commission for an order.
     */
    public static function calculateForOrder(Order $order, Vendor $vendor): self
    {
        $commissionRate = $vendor->commission_rate ?? 10.00;
        $orderAmount = $order->total;
        $commissionAmount = ($orderAmount * $commissionRate) / 100;
        $vendorAmount = $orderAmount - $commissionAmount;

        return self::create([
            'order_id' => $order->id,
            'vendor_id' => $vendor->id,
            'order_amount' => $orderAmount,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'vendor_amount' => $vendorAmount,
            'status' => 'pending',
        ]);
    }

    /**
     * Mark commission as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
