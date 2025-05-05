<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class TemperatureRecord extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'admission_id',
        'nurse_id',
        'nurse_sign',
        'active',
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('temperatureRecords.show, ' . $this->id)
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

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function temperatureDetails(): HasMany
    {
        return $this->hasMany(TemperatureDetail::class);
    }

    public function eliminationRecords(): HasMany
    {
        return $this->hasMany(EliminationRecord::class);
    }
}
