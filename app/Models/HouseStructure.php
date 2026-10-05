<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HouseStructure extends Model
{
    protected $fillable = [
        'house_id',
        'foundation',
        'sloof_condition_id',
        'column_condition_id',
        'beam_condition_id',
    ];

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function sloofCondition(): BelongsTo
    {
        return $this->belongsTo(MasterValue::class, 'sloof_condition_id');
    }

    public function columnCondition(): BelongsTo
    {
        return $this->belongsTo(MasterValue::class, 'column_condition_id');
    }

    public function beamCondition(): BelongsTo
    {
        return $this->belongsTo(MasterValue::class, 'beam_condition_id');
    }
}