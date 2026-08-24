<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class ReceiptController extends Controller
{

    public function show(Request $request)
    {

        $student = $request->user()->student;


        $enrollment = $student
            ->enrollments()
            ->latest()
            ->first();



        if(!$enrollment){

            return response()->json([
                'message'=>'No enrollment found'
            ],404);

        }



        $payment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
        ->latest()
        ->first();



        if(!$payment){

            return response()->json([
                'message'=>'No payment record found'
            ],404);

        }



        return response()->json([


            'student' =>

            $student->first_name .
            " " .
            $student->last_name,



            'reference' =>

            $payment->payment_reference
            ??
            'TEMP-REFERENCE',



            'amount' =>

            $payment->amount,



            'status' =>

            $payment->status


        ]);

    }

}