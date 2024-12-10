<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicationDetail extends Model
{
    protected $fillable = [
        'medication_record_id',
        'drug',
        'dose',
        'route',
        'fc',
        'frequency',
        'active',
    ];
}
