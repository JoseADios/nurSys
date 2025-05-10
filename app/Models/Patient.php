<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Patient extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'first_name',
        'first_surname',
        'second_surname',
        'phone',
        'identification_card',
        'nationality',
        'email',
        'birthdate',
        'position',
        'marital_status',
        'address',
        'ars',
        'active',
    ];

    // ACTIVITY LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('patients.show, ' . $this->id)
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

    // RELATIONS

    public function admission(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function isAvailable(): bool
    {
        if (!$this->active) {
            return false;
        }
        return !$this->admission()
            ->whereNull('discharged_date')
            ->where('active', true)
            ->exists();
    }
}
