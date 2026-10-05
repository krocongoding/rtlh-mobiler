<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseUtility extends Model
{
    protected $fillable = [
        'house_id',
        'light_opening',
        'ventilation',
        'lighting_source_id',
    ];

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function lightingSource(): BelongsTo
    {
        return $this->belongsTo(
            MasterValue::class,
            'lighting_source_id'
        );
    }
}