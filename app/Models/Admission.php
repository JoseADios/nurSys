<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'in_process',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d h:i',
            'active' => 'boolean',
        ];
    }

    public function recepcionist(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalOrders(): HasMany
    {
        return $this->hasMany(MedicalOrder::class);
    }

    public function medicationRecord(): HasMany
    {
        return $this->hasMany(MedicationRecord::class);
    }

    public function nurseRecord(): HasMany
    {
        return $this->hasMany(NurseRecord::class);
    }

    public function TemperatureRecord(): HasMany
    {
        return $this->hasMany(TemperatureRecord::class);
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}

