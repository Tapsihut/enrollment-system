<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\AcademicBackground;
use App\Models\CurriculumSubject;
use App\Models\EnrollmentSubject;


class RegistrarController extends Controller
{


    public function index(Request $request)
    {

        $query = Enrollment::with([

            'student',
            'course',
            'curriculum',
            'schoolYear',
            'semester',

        ]);


        if ($request->filled('search')) {


            $search = $request->search;


            $query->whereHas('student', function ($q) use ($search) {


                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw(
                        "CONCAT(first_name,' ',last_name) LIKE ?",
                        ["%{$search}%"]
                  );


            });


        }


        if ($request->filled('course')) {


            $course = $request->course;


            $query->whereHas('course', function($q) use ($course){


                $q->where('name', $course);


            });


        }


        if($request->filled('status')){


            $query->where(
                'status',
                $request->status
            );


        }


        $sort = $request->get(
            'sort',
            'desc'
        );


        $query->orderBy(
            'created_at',
            $sort
        );


        $perPage = $request->get(
            'per_page',
            10
        );


        return response()->json(

            $query->paginate($perPage)

        );

    }




    public function show($id)
    {

        $enrollment = Enrollment::with([

            'student',
            'guardian',
            'course',
            'curriculum',
            'schoolYear',
            'semester',
            'academicBackground',
            'documents'

        ])
        ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Get Assigned Enrollment Subjects
        |--------------------------------------------------------------------------
        */

        $enrollmentSubjects = EnrollmentSubject::with([

            'subject'

        ])
        ->where(
            'enrollment_id',
            $enrollment->id
        )
        ->get();


        return response()->json([


            'id'=>$enrollment->id,

            'status'=>$enrollment->status,

            'created_at'=>$enrollment->created_at,

            'student'=>$enrollment->student,

            'guardian'=>$enrollment->guardian,

            'course'=>$enrollment->course,

            'curriculum'=>$enrollment->curriculum,

            'schoolYear'=>$enrollment->schoolYear,

            'semester'=>$enrollment->semester,

            'academicBackground'=>$enrollment->academicBackground,

            'documents'=>$enrollment->documents,
            'year_level' => $enrollment->year_level,


            /*
            |--------------------------------------------------------------------------
            | Assigned Subjects
            |--------------------------------------------------------------------------
            */

            'enrollmentSubjects'=>$enrollmentSubjects


        ]);

    }






    public function approve($id)
    {

        $enrollment = Enrollment::with('semester')
            ->findOrFail($id);


        /*
        |--------------------------------------------------------------------------
        | Update Enrollment Status
        |--------------------------------------------------------------------------
        */


        $enrollment->status = "Approved";

        $enrollment->save();





        /*
        |--------------------------------------------------------------------------
        | Get Curriculum Subjects
        |--------------------------------------------------------------------------
        */


        $semester = $enrollment->semester_id;

        $subjects = CurriculumSubject::where(

                'curriculum_id',

                $enrollment->curriculum_id

            )
            ->where(

                'year_level',

                $enrollment->year_level

            )
            ->where(

                'semester',

                $semester

            )
            ->get();







        /*
        |--------------------------------------------------------------------------
        | Insert Enrollment Subjects
        |--------------------------------------------------------------------------
        */


        foreach($subjects as $subject)
        {


            EnrollmentSubject::updateOrCreate(

                [

                    'enrollment_id'=>$enrollment->id,

                    'subject_id'=>$subject->subject_id

                ],


                [

                    'units'=>$subject->subject->units

                ]

            );


        }







        return response()->json([


            'message'=>
            'Enrollment approved and subjects assigned successfully.',


            'subjects_assigned'=>
            $subjects->count()


        ]);

    }







    public function reject($id)
    {

        $enrollment = Enrollment::findOrFail($id);


        $enrollment->status="Rejected";


        $enrollment->save();



        return response()->json([

            'message'=>'Enrollment rejected successfully.'

        ]);

    }









    public function dashboard()
    {

        return response()->json([


            'statistics'=>[


                'total'=>Enrollment::count(),


                'pending'=>
                Enrollment::where(
                    'status',
                    'Pending'
                )->count(),



                'approved'=>
                Enrollment::where(
                    'status',
                    'Approved'
                )->count(),



                'rejected'=>
                Enrollment::where(
                    'status',
                    'Rejected'
                )->count(),



                'payment'=>
                Enrollment::where(
                    'status',
                    'Approved'
                )->count(),



                'today'=>
                Enrollment::whereDate(
                    'created_at',
                    today()
                )->count(),


            ],




            'recent'=>Enrollment::with([

                'student',
                'course'

            ])
            ->latest()
            ->take(5)
            ->get(),



            'updated_at'=>now()
            ->format('h:i:s A')


        ]);

    }


}