<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'email_orders',
        'email_messages',
        'email_reviews',
        'email_promotions',
        'email_newsletter',
        'push_enabled',
    ];

    protected $casts = [
        'email_orders' => 'boolean',
        'email_messages' => 'boolean',
        'email_reviews' => 'boolean',
        'email_promotions' => 'boolean',
        'email_newsletter' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get or create preferences for a user
     */
    public static function getForUser($type, $id)
    {
        return self::firstOrCreate(
            ['notifiable_type' => $type, 'notifiable_id' => $id],
            [
                'email_orders' => true,
                'email_messages' => true,
                'email_reviews' => true,
                'email_promotions' => false,
                'email_newsletter' => false,
                'push_enabled' => false,
            ]
        );
    }
}
