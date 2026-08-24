<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{

protected $fillable = [

    'student_id',

    'father_name',
    'father_contact',

    'mother_name',
    'mother_contact',

    'guardian_name',
    'guardian_relationship',

    'relationship',

    'guardian_contact',
    'guardian_address',

];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}