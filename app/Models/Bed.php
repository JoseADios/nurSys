<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bed extends Model
{
    protected $fillable = [
        'number',
        'room',
        'active',
    ];

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }

    public function isAvailable(): bool
    {
        return !$this->admission()->where('in_process', true)->exists();
    }
}
