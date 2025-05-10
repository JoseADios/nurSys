<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Bed extends Model
{
    use LogsActivity;

    protected $fillable = [
        'status',
    ];

    // ACTIVITY LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }

    public function isAvailable(): bool
    {
        return !$this->admission()->whereNull('discharged_date')->exists() && $this->status === 'available';
    }
}
