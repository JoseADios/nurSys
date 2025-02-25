<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalOrderDetail extends Model
{
    protected $fillable = [
        'medical_order_id',
        'order',
        'regime',
        'suspended_at',
        'active'
    ];

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }
    public function medicationRecordDetail(): HasMany
    {
        return $this->hasMany(MedicationRecordDetail::class);
    }
}
