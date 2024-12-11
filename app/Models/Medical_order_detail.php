<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medical_order_detail extends Model
{
    protected $fillable = [
        'medical_order_id',
        'order',
        'regime',
        'suspended',
        'active'
    ];

    public function medical_order(): BelongsTo
    {
        return $this->belongsTo(Medical_order::class);
    }
}
