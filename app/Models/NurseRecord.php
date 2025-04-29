<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseRecord extends Model
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

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}



