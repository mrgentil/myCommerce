<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisputeEvidence extends Model
{
    protected $fillable = [
        'dispute_id',
        'submitted_by',
        'submitted_by_id',
        'file_path',
        'file_type',
        'description',
    ];

    public function dispute()
    {
        return $this->belongsTo(Dispute::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
