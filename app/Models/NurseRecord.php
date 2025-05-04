<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NurseRecord extends Model
{
    use LogsActivity;
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
        ->useLogName('Registro de enfermería')
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



