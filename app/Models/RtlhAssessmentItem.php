<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RtlhAssessmentItem extends Model
{
    protected $fillable = [
        'assessment_id',
        'category',
        'item_code',
        'value',
        'other_value',
        'score',
        'notes',
    ];

    public function assessment()
    {
        return $this->belongsTo(
            RtlhAssessment::class,
            'assessment_id'
        );
    }
}
