<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EliminationRecord extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'temperature_record_id',
        'nurse_id',
        'evacuations',
        'urinations',
        'created_at',
        'updated_at',
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

    public function temperatureRecord(): BelongsTo
    {
        return $this->belongsTo(TemperatureRecord::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
