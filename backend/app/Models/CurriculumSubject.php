<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumSubject extends Model
{
    protected $table = 'curriculum_subjects';

    protected $fillable = [
        'curriculum_id',
        'subject_id',
        'year_level',
        'semester',
    ];

    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id'
        );
    }

    public function curriculum()
    {
        return $this->belongsTo(
            Curriculum::class,
            'curriculum_id'
        );
    }
}