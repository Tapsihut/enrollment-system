<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicBackground extends Model
{
    protected $fillable = [
        'student_id',
        'school_id',
        'last_school',
        'school_address',
        'strand',
        'graduation_year',
        'gwa',
        'previous_course',
        'units_earned',
        'last_school_year',
        'last_semester',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}