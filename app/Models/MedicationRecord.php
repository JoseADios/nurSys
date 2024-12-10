<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
