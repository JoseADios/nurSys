<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemperatureRecord extends Model
{
    protected $fillable = [
        'admission_id',
        'impression_diagnosis',
        'nurse_sign',
        'active'
    ];
}