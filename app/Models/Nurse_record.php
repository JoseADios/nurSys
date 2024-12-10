<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurse_record extends Model
{
    protected $fillable = [
        'admission_id',
        'nurse_id',
        'nurse_sign',
        'active'
    ];
}
