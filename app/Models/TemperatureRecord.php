<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemperatureRecord extends Model
{
    protected $fillable = [
        'admission_id',
        'impression_diagnosis',
        'nurse_sign',
        'active'
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
