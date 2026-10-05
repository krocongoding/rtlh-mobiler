<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HousePhoto extends Model
{
    protected $fillable = [
        'house_id',
        'type',
        'path',
        'disk',
        'caption',
        'uploaded_by',
    ];

    public function house()
    {
        return $this->belongsTo(House::class);
    }
}
