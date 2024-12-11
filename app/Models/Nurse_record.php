<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nurse_record extends Model
{
    protected $fillable = [
        'admission_id',
        'nurse_id',
        'nurse_sign',
        'active'
    ];

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
