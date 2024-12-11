<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medical_order extends Model
{
    protected $fillable = [
        'admission_id',
        'doctor_id',
        'doctor_sign',
        'active'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
