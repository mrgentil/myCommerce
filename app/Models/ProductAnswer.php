<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'answerer_type',
        'answerer_id',
        'answer',
        'is_official',
        'helpful_count',
    ];

    protected $casts = [
        'is_official' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(ProductQuestion::class);
    }

    public function answerer()
    {
        return $this->morphTo();
    }

    /**
     * Get the answerer name
     */
    public function getAnswererNameAttribute()
    {
        if ($this->answerer_type === 'App\Models\Vendor') {
            $vendor = Vendor::find($this->answerer_id);
            return $vendor?->shop?->name ?? $vendor?->name ?? 'Vendeur';
        }
        
        $customer = Customer::find($this->answerer_id);
        return $customer?->name ?? 'Client';
    }
}
