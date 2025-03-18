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
        'receptionist_id',
        'doctor_id',
        'admission_dx',
        'final_dx',
        'created_at',
        'comment',
        'discharged_date',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /*
        SCOPES \\
    */

    public function scopeActive($query)
    {
        return $query->where('active', true)->whereNull('discharged_date');
    }

    public function scopeFilterByName($query, $name)
    {
        if ($name) {
            return $query->whereHas('patient', function ($q) use ($name) {
                $q->whereRaw("CONCAT(first_name, ' ', first_surname, ' ', second_surname) like ?", ['%' . $name . '%']);
            });
        }
        return $query;
    }

    public function scopeFilterByRoom($query, $room)
    {
        if ($room) {
            return $query->whereHas('bed', function ($q) use ($room) {
                $q->where('room', 'like', "%$room%");
            });
        }
        return $query;
    }

    public function scopeFilterByBed($query, $bed)
    {
        if ($bed) {
            return $query->whereHas('bed', function ($q) use ($bed) {
                $q->where('number', $bed);
            });
        }
        return $query;
    }


    // RELATIONS \\

    public function receptionist(): BelongsTo
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

    public function medicationRecord(): HasOne
    {
        return $this->HasOne(MedicationRecord::class);
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
