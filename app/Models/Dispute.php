<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'vendor_id',
        'type',
        'description',
        'amount_disputed',
        'status',
        'resolution_notes',
        'refund_amount',
        'escalated_at',
        'resolved_at',
    ];

    protected $casts = [
        'amount_disputed' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function messages()
    {
        return $this->hasMany(DisputeMessage::class)->orderBy('created_at');
    }

    public function evidence()
    {
        return $this->hasMany(DisputeEvidence::class);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'not_received' => 'Article non reçu',
            'not_as_described' => 'Non conforme à la description',
            'damaged' => 'Article endommagé',
            'counterfeit' => 'Article contrefait',
            'wrong_item' => 'Mauvais article',
            'other' => 'Autre problème',
            default => $this->type,
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'open' => 'Ouvert',
            'under_review' => 'En cours d\'examen',
            'awaiting_vendor' => 'En attente du vendeur',
            'awaiting_customer' => 'En attente du client',
            'escalated' => 'Escaladé à l\'admin',
            'resolved_refund' => 'Résolu - Remboursement',
            'resolved_partial' => 'Résolu - Remboursement partiel',
            'resolved_no_refund' => 'Résolu - Sans remboursement',
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
            'open' => 'warning',
            'under_review' => 'info',
            'awaiting_vendor' => 'primary',
            'awaiting_customer' => 'primary',
            'escalated' => 'danger',
            'resolved_refund', 'resolved_partial' => 'success',
            'resolved_no_refund' => 'secondary',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Check if dispute is open
     */
    public function isOpen()
    {
        return !in_array($this->status, ['resolved_refund', 'resolved_partial', 'resolved_no_refund', 'cancelled']);
    }

    /**
     * Escalate to admin
     */
    public function escalate()
    {
        $this->update([
            'status' => 'escalated',
            'escalated_at' => now(),
        ]);
    }

    /**
     * Resolve dispute
     */
    public function resolve($status, $refundAmount = null, $notes = null)
    {
        $this->update([
            'status' => $status,
            'refund_amount' => $refundAmount,
            'resolution_notes' => $notes,
            'resolved_at' => now(),
        ]);

        // Notify both parties
        UserNotification::notifyCustomer(
            $this->customer_id,
            'order',
            'Litige résolu',
            "Votre litige #{$this->id} a été résolu.",
            route('customer.disputes.show', $this->id)
        );

        UserNotification::notifyVendor(
            $this->vendor_id,
            'order',
            'Litige résolu',
            "Le litige #{$this->id} a été résolu.",
            route('vendor.disputes.show', $this->id)
        );
    }
}
