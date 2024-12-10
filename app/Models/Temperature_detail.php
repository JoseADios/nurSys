<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Temperature_detail extends Model
{
    protected $fillable = [
        'temperature_record_id',
        'temperature',
        'evacuations',
        'urinations',
        'active'
    ];
}
