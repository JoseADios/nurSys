<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationRecordDetail extends Model
{
    protected $fillable = [
        'medication_record_id',
        'drug',
        'dose',
        'dose_metric',
        'route',
        'fc',
        'interval_in_hours',
        'active',
        'start_time',
        'medical_order_detail_id',
        'suspended_at'
    ];

    public function medicationRecord(): BelongsTo
    {
        return $this->belongsTo(medicationRecord::class);
    }
    public function medicalOrderDetail(): BelongsTo
    {
        return $this->belongsTo(medicalOrderDetail::class);
    }

    public function medicationNotification(): HasMany
    {
        return $this->hasMany(medicationNotification::class);
    }
}
