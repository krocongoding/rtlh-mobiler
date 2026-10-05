<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RtlhAssessment extends Model
{
    protected $fillable = [
        'house_id',
        'assessment_year',
        'assessment_date',
        'assessor_id',
        'status',
        'score',
        'priority_level',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'score' => 'decimal:2',
        ];
    }

    public function house()
    {
        return $this->belongsTo(House::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function items()
    {
        return $this->hasMany(
            RtlhAssessmentItem::class,
            'assessment_id'
        );
    }
}
