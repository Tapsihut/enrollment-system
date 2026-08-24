<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentSubject extends Model
{

    protected $fillable = [

        'enrollment_id',
        'subject_id',
        'units'

    ];


    public function enrollment()
    {
        return $this->belongsTo(
            Enrollment::class,
            'enrollment_id'
        );
    }


    public function subject()
    {
        return $this->belongsTo(
            Subject::class,
            'subject_id'
        );
    }

}