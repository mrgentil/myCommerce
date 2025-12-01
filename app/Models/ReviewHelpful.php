<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewHelpful extends Model
{
    protected $table = 'review_helpful';

    protected $fillable = [
        'review_id',
        'customer_id',
    ];

    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
