<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'points',
        'type',
        'description',
        'reference_type',
        'reference_id',
        'balance_after',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'earned' => 'Gagné',
            'redeemed' => 'Utilisé',
            'expired' => 'Expiré',
            'bonus' => 'Bonus',
            'refund' => 'Remboursé',
            default => $this->type,
        };
    }

    /**
     * Get type color
     */
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'earned' => 'success',
            'redeemed' => 'warning',
            'expired' => 'danger',
            'bonus' => 'info',
            'refund' => 'secondary',
            default => 'secondary',
        };
    }
}
