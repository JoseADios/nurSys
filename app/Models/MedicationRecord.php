<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicationRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'doctor_id',
        'diagnosis',
        'diet',
        'referrals',
        'pending_studies',
        'doctor_sign',
        'active',
        'suspended_at'
    ];


    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function medicationRecordDetail(): HasMany
    {
        return $this->hasMany(MedicationRecordDetail::class);
    }
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
