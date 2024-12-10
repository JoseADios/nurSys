<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_id',
        'patient_id',
        'recepcionist_id',
        'doctor-id',
        'admission_dx',
        'final_dx',
        'created_at',
        'comment',
        'active',
    ];
}
