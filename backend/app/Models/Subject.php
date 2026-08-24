<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'title',
        'units',
    ];

    public function curriculumSubjects()
    {
        return $this->hasMany(
            CurriculumSubject::class,
            'subject_id'
        );
    }

    public function curricula()
    {
        return $this->belongsToMany(
            Curriculum::class,
            'curriculum_subjects',
            'subject_id',
            'curriculum_id'
        )->withPivot([
            'year_level',
            'semester',
        ]);
    }
}