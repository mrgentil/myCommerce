<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayout extends Model
{
    protected $fillable = [
        'vendor_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the vendor associated with the payout.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get pending balance for a vendor.
     */
    public static function getPendingBalance(int $vendorId): float
    {
        return Commission::where('vendor_id', $vendorId)
            ->where('status', 'pending')
            ->sum('vendor_amount');
    }

    /**
     * Create a payout for vendor.
     */
    public static function createPayout(Vendor $vendor, float $amount, string $paymentMethod = null): self
    {
        return self::create([
            'vendor_id' => $vendor->id,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);
    }
}
