<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseOccupant extends Model
{
    protected $table = 'house_occupants';

    protected $fillable = [
        'house_id',
        'relationship',
        'gender',
        'birth_year',
        'occupation',
        'education',
        'is_primary_contact',
    ];

    protected function casts(): array
    {
        return [
            'birth_year' => 'integer',
            'is_primary_contact' => 'boolean',
        ];
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }
}