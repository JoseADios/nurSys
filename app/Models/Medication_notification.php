<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medication_notification extends Model
{
    protected $fillable = [
        'medication_detail_id',
        'nurse_id',
        'applied',
        'administered_time',
        'nurse_sign',
        'active'
    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}