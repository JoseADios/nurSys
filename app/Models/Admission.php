<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_id',
        'patient_id',
        'recepcionist_id',
        'doctor_id',
        'admission_dx',
        'final_dx',
        'created_at',
        'comment',
        'active',
    ];

    public function recepcionist(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
