<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseWall extends Model
{
    protected $fillable = [
        'house_id',
        'material_id',
        'condition_id',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }
public function material(): BelongsTo
{
    return $this->belongsTo(MasterValue::class, 'material_id');
}

public function condition(): BelongsTo
{
    return $this->belongsTo(MasterValue::class, 'condition_id');
}
}
