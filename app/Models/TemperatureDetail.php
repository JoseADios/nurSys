<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TemperatureDetail extends Model
{
    use LogsActivity;

    protected $fillable = [
        'temperature_record_id',
        'nurse_id',
        'temperature',
        'active'
    ];

    // ACTIVITY LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('temperatureRecords.show, ' . $this->temperature_record_id)
            ->dontSubmitEmptyLogs();
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function TemperatureRecord(): BelongsTo
    {
        return $this->belongsTo(TemperatureRecord::class);
    }

}
