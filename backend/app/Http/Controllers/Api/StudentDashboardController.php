<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Enrollment;
use App\Models\Payment;


class StudentDashboardController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $student = $user->student;



        $enrollment = null;

        $payment = null;



        /*
        |--------------------------------------------------------------------------
        | Default Dashboard Data
        |--------------------------------------------------------------------------
        */

        $progress = 0;

        $subjects = 0;

        $enrollmentFee = 1500.00;


        $steps = [

            "profile" => false,

            "enrollment" => false,

            "approval" => false,

            "payment" => false,

            "completed" => false

        ];



        /*
        |--------------------------------------------------------------------------
        | Check Student Profile
        |--------------------------------------------------------------------------
        */


        if($student){


            // Step 1
            $steps["profile"] = true;

            $progress = 20;



            /*
            |--------------------------------------------------------------------------
            | Get Latest Enrollment
            |--------------------------------------------------------------------------
            */


            $enrollment = Enrollment::where(

                'student_id',

                $student->id

            )
            ->latest()
            ->first();





            if($enrollment){


                // Step 2
                $steps["enrollment"] = true;

                $progress = 40;



                /*
                |--------------------------------------------------------------------------
                | Registrar Approval
                |--------------------------------------------------------------------------
                */


                if(
                    $enrollment->status == "Approved"
                    ||
                    $enrollment->status == "Enrolled"
                ){

                    $steps["approval"] = true;

                    $progress = 60;

                }





                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                    $payment = Payment::where(
                    'enrollment_id',
                    $enrollment->id
                )
                ->latest()
                ->first();

                // Default balance
                $enrollmentFee = 1500;

                // If already paid, balance becomes zero
                if ($payment && $payment->status == "Paid") {

                    $enrollmentFee = 0;

                    $steps["payment"] = true;

                    $progress = 80;

                }
                /*
                |--------------------------------------------------------------------------
                | Assigned Subjects
                |--------------------------------------------------------------------------
                |
                | Do NOT count curriculum subjects.
                |
                | Only count subjects assigned
                | to this student.
                |
                */


                if(
                    method_exists(
                        $enrollment,
                        'subjects'
                    )
                ){


                    $subjects = $enrollment
                                ->subjects()
                                ->count();


                }





                /*
                |--------------------------------------------------------------------------
                | Completed Enrollment
                |--------------------------------------------------------------------------
                */


                if(

                    $payment

                    &&

                    $payment->status == "Paid"

                    &&

                    $subjects > 0

                ){


                    $steps["completed"] = true;


                    $progress = 100;


                }



            }


        }





        return response()->json([



            "enrollment_status" =>

                $enrollment

                ?

                $enrollment->status

                :

                "No Enrollment",




            "subjects" => $subjects,




            "enrollment_fee" => $enrollmentFee,




            "payment_status" =>

                $payment

                ?

                $payment->status

                :

                "Unpaid",




            "progress" => $progress,




            "steps" => $steps



        ]);



    }

}