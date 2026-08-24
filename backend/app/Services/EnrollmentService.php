<?php

namespace App\Services;


use App\Models\Student;



class EnrollmentService
{


public function assignSubjects($student)
{


$curriculum = $student
->course
->curricula()
->where(
'year_level',
$student->year_level
)
->first();



if(!$curriculum){

return false;

}



$subjects =
$curriculum
->subjects;



$enrollment =
$student->enrollments()
->latest()
->first();



foreach($subjects as $subject){


$enrollment
->subjects()
->attach(

$subject->id

);


}



return true;


}


}