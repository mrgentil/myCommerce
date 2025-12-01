<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'product_id', 'rating', 'review', 'is_approved',
        'helpful_count', 'verified_purchase',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'verified_purchase' => 'boolean',
    ];

    /**
     * Get the customer that owns the review.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product that the review belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get review images
     */
    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class, 'review_id');
    }

    /**
     * Get helpful votes
     */
    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpful::class, 'review_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', 1);
    }

    public function scopeWithPhotos($query)
    {
        return $query->has('images');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    /**
     * Check if a customer has marked this review as helpful
     */
    public function isHelpfulByCustomer($customerId)
    {
        return $this->helpfulVotes()->where('customer_id', $customerId)->exists();
    }

    /**
     * Increment helpful count
     */
    public function markAsHelpful($customerId)
    {
        if (!$this->isHelpfulByCustomer($customerId)) {
            ReviewHelpful::create([
                'review_id' => $this->id,
                'customer_id' => $customerId,
            ]);
            $this->increment('helpful_count');
            return true;
        }
        return false;
    }
}
