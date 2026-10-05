<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterValue extends Model
{
    protected $fillable = [
        'master_category_id',
        'code',
        'label',
        'sort_order',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(
            MasterCategory::class,
            'master_category_id'
        );
    }
}
