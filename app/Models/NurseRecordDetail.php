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
            ->useLogName('Registro de enfermería - Evento')
            ->dontSubmitEmptyLogs();
    }

    // RELATIONS

    public function nurseRecord(): BelongsTo
    {
        return $this->belongsTo(NurseRecord::class);
    }


}
