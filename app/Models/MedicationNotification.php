<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationNotification extends Model
{
    protected $fillable = [
        'medication_record_detail_id',
        'nurse_id',
        'applied',
        'administered_time',
        'scheduled_time',
        'nurse_sign',
        'active',

    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicationRecordDetail(): BelongsTo
    {
        return $this->belongsTo(medicationRecordDetail::class);
    }
}
