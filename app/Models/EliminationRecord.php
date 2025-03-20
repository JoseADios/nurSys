<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EliminationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'temperature_record_id',
        'nurse_id',
        'evacuations',
        'urinations',
        'created_at',
        'updated_at',
    ];
}
