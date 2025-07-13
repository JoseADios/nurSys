<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class MedicationRecord extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'admission_id',
        'nurse_id',

        'diet',
        'referrals',
        'pending_studies',

        'active',

    ];

    // LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('medicationRecords.show, ' . $this->id)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Modificar la descripción del evento si es 'updated' y 'active' cambió
    public function tapActivity(Activity $activity, string $eventName)
    {
        $properties = $activity->properties->toArray();
        if ($eventName === 'updated' && $this->isDirty('active')) {
            $activity->description = $this->active ? 'activated' : 'deactivated';
        } else {
            $activity->description = $eventName;
        }
        $activity->properties = collect($properties);
    }


    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function medicationRecordDetail(): HasMany
    {
        return $this->hasMany(MedicationRecordDetail::class);
    }
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
     public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
