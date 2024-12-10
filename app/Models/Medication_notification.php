<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication_notification extends Model
{
    protected $fillable = [
        'medication_detail_id',
        'nurse_id',
        'applied',
        'administered_time',
        'nurse_sign',
        'active'
    ];
}
