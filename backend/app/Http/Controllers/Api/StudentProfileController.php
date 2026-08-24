<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentProfileRequest;
use App\Models\Student;

class StudentProfileController extends Controller
{


    public function store(StoreStudentProfileRequest $request)
    {

        $student = Student::updateOrCreate(

            [
                'user_id' => auth()->id()
            ],


            $request->validated()

        );


        return response()->json([

            'message'=>'Profile saved successfully.',

            'student'=>$student

        ]);

    }





    public function update(StoreStudentProfileRequest $request)
    {


        $student = Student::updateOrCreate(

            [

                'user_id'=>auth()->id()

            ],


            $request->validated()

        );



        return response()->json([

            'message'=>'Profile updated successfully.',

            'student'=>$student

        ]);

    }
        public function show()
        {
            $student = auth()->user()->student;

            $documentsAvailable = false;
            $enrollmentCompleted = false;

            if ($student) {

                $enrollment = $student
                    ->enrollments()
                    ->latest()
                    ->first();

                if ($enrollment) {

                    if ($enrollment->status === 'Enrolled') {

                        $documentsAvailable = true;

                        $enrollmentCompleted = true;

                    }

                }

            }

            return response()->json([

                'student' => $student,

                'documents_available' => $documentsAvailable,

                'enrollment_completed' => $enrollmentCompleted

            ]);
        }

public function completion()
{
    $student = auth()->user()->student;

    if (!$student) {
        return response()->json([
            'complete' => false,
            'percentage' => 0,
            'missing' => ['student_profile']
        ]);
    }

    $requiredFields = [

        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'civil_status',
        'nationality',
        'contact_number',
        'address'

    ];

    $missing = [];

    foreach ($requiredFields as $field) {

        if (blank($student->$field)) {
            $missing[] = $field;
        }

    }

    $completed = count($requiredFields) - count($missing);

    $percentage = round(
        ($completed / count($requiredFields)) * 100
    );

    return response()->json([

        'complete' => empty($missing),

        'percentage' => $percentage,

        'missing' => $missing

    ]);
}

}