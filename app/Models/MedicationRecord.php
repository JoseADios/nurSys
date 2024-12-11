<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicationRecord extends Model
{
    protected $fillable = [
        'admission_id',
        'diagnosis',
        'diet',
        'referrals',
        'pending_studies',
        'doctor_sign',
        'active',
    ];


    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
