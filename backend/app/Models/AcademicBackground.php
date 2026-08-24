<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicBackground extends Model
{
    protected $fillable = [

        'student_id',
        'student_type',

        // Freshmen
        'last_school',
        'school_address',
        'strand',
        'graduation_year',
        'gwa',

        // Transferee
        'previous_course',
        'units_earned',

        // Returnee
        'last_school_year',
        'last_semester',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}