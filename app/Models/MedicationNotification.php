<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicationNotification extends Model
{
    use LogsActivity;

    protected $fillable = [
        'medication_record_detail_id',
        'nurse_id',
        'applied',
        'administered_time',
        'scheduled_time',
        'nurse_sign',
        'active',

    ];

    // ACTIVITY LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('medicationNotification.show, '.$this->medication_record_detail_id)
            ->dontSubmitEmptyLogs();
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicationRecordDetail(): BelongsTo
    {
        return $this->belongsTo(medicationRecordDetail::class);
    }
}
