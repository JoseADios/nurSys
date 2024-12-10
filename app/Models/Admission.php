<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_bed',
        'id_patient',
        'id_recepcionist',
        'id_doctor',
        'admission_dx',
        'final_dx',
        'created_at',
        'comment',
        'active',
    ];
}
