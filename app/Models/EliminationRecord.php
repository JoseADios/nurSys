<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EliminationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'temperature_record_id',
        'nurse_id',
        'evacuations',
        'urinations',
        'created_at',
        'updated_at',
    ];

    public function temperatureRecord(): BelongsTo
    {
        return $this->belongsTo(TemperatureRecord::class);
    }
}
