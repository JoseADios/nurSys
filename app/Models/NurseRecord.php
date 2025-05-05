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
            ->logAll()
            ->useLogName('nurseRecords.show, '.$this->id)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
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



