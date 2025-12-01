<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'vendor_id',
        'product_id',
        'order_id',
        'subject',
        'status',
        'customer_last_read',
        'vendor_last_read',
    ];

    protected $casts = [
        'customer_last_read' => 'datetime',
        'vendor_last_read' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadCountForCustomer()
    {
        return $this->messages()
            ->where('sender_type', 'vendor')
            ->where('is_read', false)
            ->count();
    }

    public function unreadCountForVendor()
    {
        return $this->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->count();
    }

    public function markAsReadForCustomer()
    {
        $this->messages()
            ->where('sender_type', 'vendor')
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $this->update(['customer_last_read' => now()]);
    }

    public function markAsReadForVendor()
    {
        $this->messages()
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $this->update(['vendor_last_read' => now()]);
    }

    public static function findOrCreateConversation($customerId, $vendorId, $productId = null, $orderId = null)
    {
        $query = self::where('customer_id', $customerId)
            ->where('vendor_id', $vendorId)
            ->where('status', 'open');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($orderId) {
            $query->where('order_id', $orderId);
        }

        $conversation = $query->first();

        if (!$conversation) {
            $conversation = self::create([
                'customer_id' => $customerId,
                'vendor_id' => $vendorId,
                'product_id' => $productId,
                'order_id' => $orderId,
            ]);
        }

        return $conversation;
    }
}
