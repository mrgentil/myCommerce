<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_detail_id',
        'customer_id',
        'vendor_id',
        'type',
        'reason',
        'description',
        'quantity',
        'refund_amount',
        'status',
        'admin_notes',
        'vendor_response',
        'return_tracking',
        'approved_at',
        'shipped_at',
        'received_at',
        'refunded_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function images()
    {
        return $this->hasMany(OrderReturnImage::class);
    }

    /**
     * Get reason label in French
     */
    public function getReasonLabelAttribute()
    {
        return match($this->reason) {
            'defective' => 'Produit défectueux',
            'wrong_item' => 'Mauvais article reçu',
            'not_as_described' => 'Non conforme à la description',
            'changed_mind' => 'Changement d\'avis',
            'too_late' => 'Livraison trop tardive',
            'damaged' => 'Produit endommagé',
            'other' => 'Autre raison',
            default => $this->reason,
        };
    }

    /**
     * Get status label in French
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'approved' => 'Approuvé',
            'rejected' => 'Refusé',
            'shipped' => 'Renvoyé',
            'received' => 'Reçu',
            'refunded' => 'Remboursé',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
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
            'approved' => 'info',
            'rejected' => 'danger',
            'shipped' => 'primary',
            'received' => 'info',
            'refunded' => 'success',
            'completed' => 'success',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'return' => 'Retour',
            'refund' => 'Remboursement',
            'exchange' => 'Échange',
            default => $this->type,
        };
    }
}
