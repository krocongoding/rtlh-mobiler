<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseCeiling extends Model
{
    protected $fillable = [
        'house_id',
        'condition_id',
    ];

    public function house(): BelongsTo
    {
        return $this->belongsTo(
            House::class
        );
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(
            MasterValue::class,
            'condition_id'
        );
    }
}