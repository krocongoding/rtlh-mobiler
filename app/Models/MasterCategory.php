<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
    ];

    public function values()
    {
        return $this->hasMany(MasterValue::class);
    }
}
