<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    protected $fillable = [
        'dispute_id',
        'sender_type',
        'sender_id',
        'message',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function dispute()
    {
        return $this->belongsTo(Dispute::class);
    }

    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'admin') {
            return 'Support';
        }
        
        if ($this->sender_type === 'vendor') {
            $vendor = Vendor::find($this->sender_id);
            return $vendor?->shop?->name ?? 'Vendeur';
        }

        $customer = Customer::find($this->sender_id);
        return $customer?->name ?? 'Client';
    }
}
