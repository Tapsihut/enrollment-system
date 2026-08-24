<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Enrollment;


class AssessmentController extends Controller
{


    public function create($id)
    {

        $enrollment = Enrollment::findOrFail($id);


        $assessment = Assessment::create([

            'enrollment_id'=>$enrollment->id,

            'enrollment_fee'=>500,

            'miscellaneous_fee'=>300,

            'other_fee'=>0,

            'total_amount'=>800,

        ]);


        return response()->json([

            'message'=>'Assessment created',

            'assessment'=>$assessment

        ]);

    }


}