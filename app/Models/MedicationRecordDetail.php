<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicationRecordDetail extends Model
{
    use LogsActivity;

    protected $fillable = [
        'medication_record_id',
        'drug',
        'dose',
        'dose_metric',
        'route',
        'fc',
        'interval_in_hours',
        'nebulization_time',
        'nebulized',
        'active',
        'start_time',
        'medical_order_detail_id',
        'suspended_at'
    ];

    // ACTIVITY LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('medicationRecords.show, '.$this->medication_record_id)
            ->dontSubmitEmptyLogs();
    }

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
