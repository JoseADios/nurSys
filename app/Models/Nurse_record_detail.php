<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nurse_record_detail extends Model
{
    protected $fillable = [
        'nurse_record_id',
        'medication',
        'comment',
        'active'
    ];
}
