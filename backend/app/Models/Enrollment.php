<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\Guardian;
use App\Models\AcademicBackground;
use App\Models\StudentDocument;
use App\Models\Payment;
use App\Models\Subject;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'curriculum_id',
        'school_year_id',
        'semester_id',
        'year_level',
        'status',
        'payment_status',
        'rejection_reason',
        'rejected_at',
    ];


    protected $casts = [
        'rejected_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Student
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Course
    |--------------------------------------------------------------------------
    */

    public function course()
    {
        return $this->belongsTo(Course::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Curriculum
    |--------------------------------------------------------------------------
    */

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }


    /*
    |--------------------------------------------------------------------------
    | School Year
    |--------------------------------------------------------------------------
    */

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Semester
    |--------------------------------------------------------------------------
    */

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Guardian
    |--------------------------------------------------------------------------
    */

    public function guardian()
    {
        return $this->hasOne(
            Guardian::class,
            'student_id',
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Academic Background
    |--------------------------------------------------------------------------
    */

    public function academicBackground()
    {
        return $this->hasOne(
            AcademicBackground::class,
            'student_id',
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Documents
    |--------------------------------------------------------------------------
    */

    public function documents()
    {
        return $this->hasMany(
            StudentDocument::class,
            'student_id',
            'student_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    public function payment()
    {
        return $this->hasOne(
            Payment::class
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Subjects
    |--------------------------------------------------------------------------
    */

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'enrollment_subjects'
        );
    }
}