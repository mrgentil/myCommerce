<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'type',
        'title',
        'message',
        'icon',
        'action_url',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity (Customer or Vendor)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Check if notification is read
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute($value)
    {
        if ($value) return $value;

        return match($this->type) {
            'order' => 'bi-bag-check',
            'message' => 'bi-chat-dots',
            'review' => 'bi-star',
            'return' => 'bi-arrow-return-left',
            'promotion' => 'bi-gift',
            'system' => 'bi-info-circle',
            default => 'bi-bell',
        };
    }

    /**
     * Get color based on type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'order' => 'success',
            'message' => 'primary',
            'review' => 'warning',
            'return' => 'info',
            'promotion' => 'danger',
            'system' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Create notification for customer
     */
    public static function notifyCustomer($customerId, $type, $title, $message, $actionUrl = null, $data = null)
    {
        return self::create([
            'notifiable_type' => 'App\Models\Customer',
            'notifiable_id' => $customerId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * Create notification for vendor
     */
    public static function notifyVendor($vendorId, $type, $title, $message, $actionUrl = null, $data = null)
    {
        return self::create([
            'notifiable_type' => 'App\Models\Vendor',
            'notifiable_id' => $vendorId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }
}
