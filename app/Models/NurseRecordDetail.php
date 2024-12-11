<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseRecordDetail extends Model
{
    protected $fillable = [
        'nurse_record_id',
        'medication',
        'comment',
        'active'
    ];

    public function NurseRecordDetail(): BelongsTo
    {
        return $this->belongsTo(NurseRecord::class);
    }


}
