<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalOrder extends Model
{
    protected $fillable = [
        'admission_id',
        'doctor_id',
        'doctor_sign',
        'active'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalOrderDetail(): HasMany
    {
        return $this->hasMany(MedicalOrderDetail::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
