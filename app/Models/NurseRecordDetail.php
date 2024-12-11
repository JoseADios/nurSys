<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseRecordDetail extends Model
{
    protected $fillable = [
        'nurse_record_id',
        'medication',
        'comment',
        'active'
    ];
}
