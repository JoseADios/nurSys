<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medical_order extends Model
{
    protected $fillable = [
        'admission_id',
        'doctor_id',
        'doctor_sign',
        'active'
    ];
}
