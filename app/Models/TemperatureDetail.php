<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemperatureDetail extends Model
{
    protected $fillable = [
        'temperature_record_id',
        'nurse_id',
        'temperature',
        'active'
    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function TemperatureRecord(): BelongsTo
    {
        return $this->belongsTo(TemperatureRecord::class);
    }

}
