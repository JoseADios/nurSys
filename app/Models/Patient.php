<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'first_surname',
        'second_surname',
        'phone',
        'identification_card',
        'nacionality',
        'email',
        'birthdate',
        'position',
        'marital_status',
        'address',
        'ars',
        'active',
        'active'
    ];
}
