<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemperatureRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'nurse_id',
        'nurse_sign',
        'active',
        'created_at',
        'updated_at',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function temperatureDetails(): HasMany
    {
        return $this->hasMany(TemperatureDetail::class);
    }

    public function eliminationRecords(): HasMany
    {
        return $this->hasMany(EliminationRecord::class);
    }
}
