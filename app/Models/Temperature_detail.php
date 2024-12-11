<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Temperature_detail extends Model
{
    protected $fillable = [
        'temperature_record_id',
        'nurse_id',
        'temperature',
        'evacuations',
        'urinations',
        'active'
    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
