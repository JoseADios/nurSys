<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class NurseRecordDetail extends Model
{
    use LogsActivity;

    protected $fillable = [
        'nurse_record_id',
        'medication',
        'comment',
        'active'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Registro de enfermería - Evento')
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $properties = $activity->properties->toArray();

        // Agregar nurse_record_id dentro de attributes
        $properties['attributes']['nurse_record_id'] = $this->nurse_record_id;

        $activity->properties = collect($properties);
    }


    // RELATIONS

    public function nurseRecord(): BelongsTo
    {
        return $this->belongsTo(NurseRecord::class);
    }


}
