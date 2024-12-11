<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function admission(): HasMany
    {
        return $this->hasMany(Admission::class);
    }
}
