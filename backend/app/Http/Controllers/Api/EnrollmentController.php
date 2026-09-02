<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Enrollment;
use App\Models\StudentDocument;
use App\Models\AcademicBackground;
use App\Models\Payment;


class EnrollmentController extends Controller
{

    public function store(Request $request)
    {

        Log::info('Enrollment Request', $request->all());


        $request->validate([


            'student_type'=>'required',


            // Personal Information

            'first_name'=>'required',

            'last_name'=>'required',

            'birth_date'=>'required',

            'gender'=>'required',

            'address'=>'required',

            'contact_number'=>'required',

            'email'=>'required|email',



            // Enrollment

            'course_id'=>'required|exists:courses,id',

            'curriculum_id'=>'required|exists:curricula,id',

            'school_year_id'=>'required|exists:school_years,id',

            'semester_id'=>'required|exists:semesters,id',

            'year_level'=>'required',
            // Academic Background

            'last_school' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:255',
            'strand' => 'nullable|string|max:100',
            'graduation_year' => 'nullable|digits:4',
            'gwa' => 'nullable|numeric|min:0|max:100',

            'previous_course' => 'nullable|string|max:255',
            'units_earned' => 'nullable|integer|min:0',

            'last_school_year' => 'nullable|string|max:20',
            'last_semester' => 'nullable|string|max:50',


            // Documents

            'psa_birth_certificate'
                =>'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'good_moral'
                =>'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'academic_document'
                =>'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            'id_picture'
                =>'nullable|file|mimes:jpg,jpeg,png|max:5120',


        ]);



        DB::beginTransaction();



        try {


            /*
            |--------------------------------------------------------------------------
            | Get Logged User
            |--------------------------------------------------------------------------
            */


            $user = auth()->user();



            if(!$user){


                return response()->json([

                    'message'=>'User not authenticated.'

                ],401);


            }





            /*
            |--------------------------------------------------------------------------
            | Get Student
            |--------------------------------------------------------------------------
            */


            $student = Student::where(

                'user_id',

                $user->id

            )->first();




            if(!$student){


                return response()->json([


                    'success'=>false,

                    'message'=>'Your profile is incomplete.',

                    'redirect'=>'/student/profile'


                ],422);


            }
            /*
            |--------------------------------------------------------------------------
            | Check Existing Enrollment
            |--------------------------------------------------------------------------
            */
            $existingEnrollment = Enrollment::where(
                'student_id',
                $student->id
            )
            ->whereIn('status', [
                'Pending',
                'Approved',
                'Enrolled'
            ])
            ->latest()
            ->first();

            if($existingEnrollment){

                DB::rollBack();

                return response()->json([

                    'success' => false,

                    'message' => 'You already have an active enrollment.',

                    'status' => $existingEnrollment->status,

                    'enrollment_id' => $existingEnrollment->id

                ],422);

            }


            /*
            |--------------------------------------------------------------------------
            | Update Student Information
            |--------------------------------------------------------------------------
            */


            $student->update([


                'student_type'=>$request->student_type,


                'first_name'=>$request->first_name,

                'middle_name'=>$request->middle_name,

                'last_name'=>$request->last_name,


                'birth_date'=>$request->birth_date,


                'gender'=>$request->gender,


                'civil_status'=>$request->civil_status,


                'nationality'=>$request->nationality,


                'religion'=>$request->religion,


                'address'=>$request->address,


                'contact_number'=>$request->contact_number,


                'email'=>$request->email,


            ]);


/*
|--------------------------------------------------------------------------
| Save Guardian
|--------------------------------------------------------------------------
*/

Guardian::updateOrCreate(

    [
        'student_id' => $student->id
    ],

    [

        'guardian_name' => $request->guardian_name,

        'relationship' => $request->guardian_relationship,

        'guardian_contact' => $request->guardian_contact,

        'guardian_address' => $request->guardian_address,

        'father_name' => $request->father_name,

        'father_contact' => $request->father_contact,

        'mother_name' => $request->mother_name,

        'mother_contact' => $request->mother_contact,

    ]

);


/*
|--------------------------------------------------------------------------
| Save Academic Background
|--------------------------------------------------------------------------
*/

AcademicBackground::updateOrCreate(

    [
        'student_id' => $student->id
    ],

    [

        'student_type' => $request->student_type,

        'last_school' => $request->last_school,

        'school_address' => $request->school_address,

        'strand' => $request->strand,

        'graduation_year' => $request->graduation_year,

        'gwa' => $request->gwa,

        'previous_course' => $request->previous_course,

        'units_earned' => $request->units_earned,

        'last_school_year' => $request->last_school_year,

        'last_semester' => $request->last_semester,

    ]

);


            /*
            |--------------------------------------------------------------------------
            | Save Enrollment
            |--------------------------------------------------------------------------
            */


            $enrollment = Enrollment::create([
            'student_id'     => $student->id,
            'course_id'      => $request->course_id,
            'curriculum_id'  => $request->curriculum_id,
            'school_year_id' => $request->school_year_id,
            'semester_id'    => $request->semester_id,
            'year_level'     => $request->year_level,
            'status'=>'Pending',

            ]);
/*
|--------------------------------------------------------------------------
| Save Uploaded Documents
|--------------------------------------------------------------------------
*/

$documents = [

    'psa_birth_certificate',
    'good_moral',
    'academic_document',
    'id_picture'

];


foreach($documents as $document){


    if($request->hasFile($document)){


        $file = $request->file($document);


        $path = $file->store(
            'student_documents',
            'public'
        );


        StudentDocument::create([

            'student_id' => $student->id,

            'document_type' => $document,

            'file_name' => $file->getClientOriginalName(),

            'file_path' => $path,

            'status' => 'Pending'

        ]);


    }

}

            DB::commit();




            return response()->json([


                'message'=>'Enrollment submitted successfully.',


                'student'=>$student,


                'enrollment'=>$enrollment,


                'documents'=>$student->documents



            ],201);







        } catch(\Throwable $e){



            DB::rollBack();



            Log::error('Enrollment Error',[


                'message'=>$e->getMessage(),


                'file'=>$e->getFile(),


                'line'=>$e->getLine(),


                'trace'=>$e->getTraceAsString(),



            ]);




            return response()->json([


                'message'=>$e->getMessage(),


                'file'=>basename($e->getFile()),


                'line'=>$e->getLine()



            ],500);



        }


    }
    /*
|--------------------------------------------------------------------------
| APPROVE ENROLLMENT
|--------------------------------------------------------------------------
*/

public function approve($id)
{
    try {

        $enrollment = Enrollment::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Only Pending Applications Can Be Approved
        |--------------------------------------------------------------------------
        */

        if ($enrollment->status !== 'Pending') {

            return response()->json([

                'success' => false,

                'message' => 'Only pending enrollment applications can be approved.'

            ], 422);

        }


        /*
        |--------------------------------------------------------------------------
        | Approve Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment->update([

            'status' => 'Approved',

            // Clear any previous rejection information
            'rejection_reason' => null,

            'rejected_at' => null,

        ]);


        /*
        |--------------------------------------------------------------------------
        | Return Updated Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment->load([

            'student',

            'course',

            'curriculum',

            'schoolYear',

            'semester',

        ]);


        return response()->json([

            'success' => true,

            'message' => 'Enrollment approved successfully.',

            'enrollment' => $enrollment,

        ]);


    } catch (\Throwable $e) {

        Log::error('Approve Enrollment Error', [

            'message' => $e->getMessage(),

            'file' => $e->getFile(),

            'line' => $e->getLine(),

        ]);


        return response()->json([

            'success' => false,

            'message' => 'Unable to approve enrollment.',

        ], 500);

    }
}


/*
|--------------------------------------------------------------------------
| REJECT ENROLLMENT
|--------------------------------------------------------------------------
*/

public function reject(Request $request, $id)
{
    /*
    |--------------------------------------------------------------------------
    | Validate Rejection Reason
    |--------------------------------------------------------------------------
    */

    $request->validate([

        'rejection_reason' => [
            'required',
            'string',
            'min:5',
            'max:5000'
        ],

    ], [

        'rejection_reason.required' =>
            'Please provide a reason for rejecting this enrollment.',

        'rejection_reason.min' =>
            'The rejection reason must be at least 5 characters.',

        'rejection_reason.max' =>
            'The rejection reason cannot exceed 5000 characters.',

    ]);


    try {

        /*
        |--------------------------------------------------------------------------
        | Find Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment = Enrollment::findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Only Pending Applications Can Be Rejected
        |--------------------------------------------------------------------------
        */

        if ($enrollment->status !== 'Pending') {

            return response()->json([

                'success' => false,

                'message' =>
                    'Only pending enrollment applications can be rejected.'

            ], 422);

        }


        /*
        |--------------------------------------------------------------------------
        | Reject Enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment->update([

            'status' => 'Rejected',

            'rejection_reason' =>
                $request->rejection_reason,

            'rejected_at' => now(),

        ]);


        /*
        |--------------------------------------------------------------------------
        | Load Relationships
        |--------------------------------------------------------------------------
        */

        $enrollment->load([

            'student',

            'course',

            'curriculum',

            'schoolYear',

            'semester',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Return Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'message' =>
                'Enrollment rejected successfully.',

            'enrollment' => $enrollment,

        ]);


    } catch (\Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | Log Error
        |--------------------------------------------------------------------------
        */

        Log::error('Reject Enrollment Error', [

            'message' => $e->getMessage(),

            'file' => $e->getFile(),

            'line' => $e->getLine(),

            'trace' => $e->getTraceAsString(),

        ]);


        return response()->json([

            'success' => false,

            'message' =>
                'Unable to reject enrollment.',

        ], 500);

    }
}
    public function payment()
{
    return $this->hasOne(
        Payment::class
    );
}

}