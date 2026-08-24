<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'curricula';

    protected $fillable = [
        'course_id',
        'name',
        'effective_year',
        'active',
    ];

    public function course()
    {
        return $this->belongsTo(
            Course::class,
            'course_id'
        );
    }

    public function curriculumSubjects()
    {
        return $this->hasMany(
            CurriculumSubject::class,
            'curriculum_id'
        );
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'curriculum_subjects',
            'curriculum_id',
            'subject_id'
        )->withPivot([
            'year_level',
            'semester',
        ]);
    }
}