<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'first_surname',
        'second_surname',
        'phone',
        'identification_card',
        'nationality',
        'email',
        'birthdate',
        'position',
        'marital_status',
        'address',
        'ars',
        'active',
    ];

    public function admission(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function isAvailable(): bool
    {
        return !$this->admission()
            ->whereNull('discharged_date')
            ->where('active', true)
            ->exists();
    }
}
