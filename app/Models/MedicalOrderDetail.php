<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalOrderDetail extends Model
{
    protected $fillable = [
        'medical_order_id',
        'order',
        'regime',
        'suspended',
        'active'
    ];

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }
}