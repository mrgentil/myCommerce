<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'question',
        'is_public',
        'helpful_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function answers()
    {
        return $this->hasMany(ProductAnswer::class, 'question_id')->orderBy('is_official', 'desc')->orderBy('helpful_count', 'desc');
    }

    public function officialAnswer()
    {
        return $this->hasOne(ProductAnswer::class, 'question_id')->where('is_official', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeAnswered($query)
    {
        return $query->has('answers');
    }

    public function scopeUnanswered($query)
    {
        return $query->doesntHave('answers');
    }
}
