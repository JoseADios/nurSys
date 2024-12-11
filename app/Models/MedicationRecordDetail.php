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
        'route',
        'fc',
        'frequency',
        'active',
    ];

    public function medicationRecord(): BelongsTo
    {
        return $this->belongsTo(medicationRecord::class);
    }

    public function medicationNotification(): HasMany
    {
        return $this->hasMany(medicationNotification::class);
    }
}
