<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bed extends Model
{
    const STATUS_AVAILABLE = 'available';
    const STATUS_OUT_OF_SERVICE = 'out_of_service';
    const STATUS_CLEANING = 'cleaning';

    protected $fillable = [
        'status',
    ];

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }

    public function isAvailable(): bool
    {
        return !$this->admission()->whereNull('discharged_date')->exists() && $this->status === 'available';
    }
}
