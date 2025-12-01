<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    protected $fillable = [
        'code',
        'initial_balance',
        'current_balance',
        'purchaser_id',
        'recipient_email',
        'recipient_name',
        'message',
        'purchased_at',
        'redeemed_at',
        'redeemed_by',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'purchased_at' => 'datetime',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function purchaser()
    {
        return $this->belongsTo(Customer::class, 'purchaser_id');
    }

    public function redeemedByCustomer()
    {
        return $this->belongsTo(Customer::class, 'redeemed_by');
    }

    public function transactions()
    {
        return $this->hasMany(GiftCardTransaction::class);
    }

    /**
     * Generate unique code
     */
    public static function generateCode()
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Check if card is valid
     */
    public function isValid()
    {
        if (!$this->is_active) return false;
        if ($this->current_balance <= 0) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        
        return true;
    }

    /**
     * Apply to order
     */
    public function applyToOrder($orderId, $amount)
    {
        if (!$this->isValid()) {
            throw new \Exception('Carte cadeau invalide');
        }

        $amountToDeduct = min($amount, $this->current_balance);
        
        $this->decrement('current_balance', $amountToDeduct);

        GiftCardTransaction::create([
            'gift_card_id' => $this->id,
            'order_id' => $orderId,
            'amount' => -$amountToDeduct,
            'type' => 'redemption',
            'balance_after' => $this->fresh()->current_balance,
        ]);

        return $amountToDeduct;
    }

    /**
     * Create a new gift card
     */
    public static function createCard($amount, $purchaserId = null, $recipientEmail = null, $recipientName = null, $message = null)
    {
        $card = self::create([
            'code' => self::generateCode(),
            'initial_balance' => $amount,
            'current_balance' => $amount,
            'purchaser_id' => $purchaserId,
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'message' => $message,
            'purchased_at' => now(),
            'expires_at' => now()->addYear(), // 1 year validity
        ]);

        GiftCardTransaction::create([
            'gift_card_id' => $card->id,
            'amount' => $amount,
            'type' => 'purchase',
            'balance_after' => $amount,
        ]);

        return $card;
    }
}
