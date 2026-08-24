<?php

namespace App\Models;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code',
        'name',
        'course_code',
        'course_name',
        'status'
    ];


    public function students()
    {
        return $this->hasMany(Student::class);
    }


    public function curricula()
    {
        return $this->hasMany(Curriculum::class);
    }
    public function enrollments()
{
    return $this->hasMany(
        Enrollment::class,
        'course_id'
    );
}
}