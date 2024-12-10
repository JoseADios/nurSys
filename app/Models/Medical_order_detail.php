<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medical_order_detail extends Model
{
    protected $fillable = [
        'medical_order_id',
        'order',
        'regime',
        'suspended',
        'active'
    ];
}
