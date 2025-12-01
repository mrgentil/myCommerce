<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturnImage extends Model
{
    protected $fillable = [
        'order_return_id',
        'image_path',
    ];

    public function orderReturn()
    {
        return $this->belongsTo(OrderReturn::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }
}
