<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_id',
        'content',
        'attachment',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        if ($this->sender_type === 'customer') {
            return $this->belongsTo(Customer::class, 'sender_id');
        }
        return $this->belongsTo(Vendor::class, 'sender_id');
    }

    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'customer') {
            $customer = Customer::find($this->sender_id);
            return $customer ? $customer->name : 'Client';
        }
        $vendor = Vendor::find($this->sender_id);
        return $vendor ? $vendor->name : 'Vendeur';
    }

    public function getSenderAvatarAttribute()
    {
        if ($this->sender_type === 'customer') {
            $customer = Customer::find($this->sender_id);
            return $customer?->avatar ?? '/images/default-avatar.png';
        }
        $vendor = Vendor::find($this->sender_id);
        return $vendor?->avatar ?? '/images/default-avatar.png';
    }
}
