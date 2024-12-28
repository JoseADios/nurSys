<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemperatureRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'impression_diagnosis',
        'nurse_sign',
        'active',
        'created_at',
        'updated_at',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
