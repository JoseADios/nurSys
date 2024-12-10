<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temperature_record extends Model
{
    protected $fillable = [
        'admission_id',
        'impression_diagnosis',
        'nurse_sign',
        'active'
    ];
}
