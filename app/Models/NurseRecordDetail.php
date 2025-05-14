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


    // ACTIVITY LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('nurseRecords.show, '.$this->nurse_record_id)
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

    public function nurseRecord(): BelongsTo
    {
        return $this->belongsTo(NurseRecord::class);
    }


}
