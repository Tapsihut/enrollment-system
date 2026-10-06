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
use App\Models\EnrollmentDocumentRequirement;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'curriculum_id',
        'school_year_id',
        'semester_id',
        'year_level',
        'schedule_preference',
        'status',
        'rejection_reason',
        'rejected_at',
    ];

    protected $casts = [
        'rejected_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function guardian()
    {
        return $this->hasOne(
            Guardian::class,
            'student_id',
            'student_id'
        );
    }

    public function academicBackground()
    {
        return $this->hasOne(
            AcademicBackground::class,
            'student_id',
            'student_id'
        );
    }

    public function documents()
    {
        return $this->hasMany(
            StudentDocument::class,
            'student_id',
            'student_id'
        );
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function documentRequirements()
    {
        return $this->hasMany(
            EnrollmentDocumentRequirement::class
        );
    }
}