<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Log;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class NurseRecord extends Model
{
    use LogsActivity;
    public static $label = 'Nombre Amigable';

    protected $fillable = [
        'admission_id',
        'nurse_id',
        'nurse_sign',
        'active'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        Log::info('Entre al tap activity');

        $properties = $activity->properties->toArray();

        // Modificar la descripción del evento si es 'updated' y 'active' cambió
        if ($eventName === 'updated' && $this->isDirty('active')) {
            $activity->description = $this->active ? 'activated' : 'deactivated';
        } else {
            $activity->description = ucfirst($eventName); // Por ejemplo: "Created", "Updated", etc.
        }

        $activity->properties = collect($properties);
    }

    public function setDescriptionForEvent(string $eventName): string
    {
        Log::info('Entre al set description');

        if ($eventName === 'updated' && $this->isDirty('active')) {
            return $this->active ? 'activated' : 'deactivated';
        }

        return ucfirst($eventName); // Por ejemplo: "Created", "Updated", etc.
    }


    // RELATIONS

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}



