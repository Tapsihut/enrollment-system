<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Enrollment;
use App\Models\Payment;


class PaymentController extends Controller
{

    public function paymentInfo(Request $request)
    {

        $student = $request->user()->student;


        $enrollment = $student
            ->enrollments()
            ->where('status','Approved')
            ->latest()
            ->first();



        if(!$enrollment){

            return response()->json([
                'message'=>'No approved enrollment found'
            ],404);

        }



        return response()->json([

            'enrollment_id'=>$enrollment->id,
            'status'=>$enrollment->status,
            'amount'=>1500

        ]);

    }





    public function createCheckout($id)
    {

        $enrollment = Enrollment::findOrFail($id);


        $amount = 1500;



        // Create payment record first

        $payment = Payment::create([

            'enrollment_id'=>$enrollment->id,

            'amount'=>$amount,

            'status'=>'Pending',

            'payment_method'=>'GCash'

        ]);





        /*
        |--------------------------------------------------------------------------
        | REAL PAYMONGO CHECKOUT
        | Enable after PayMongo account verification
        |--------------------------------------------------------------------------
        */


        /*


        $response = Http::withBasicAuth(

            config('services.paymongo.secret'),

            ''

        )->post(

        'https://api.paymongo.com/v1/checkout_sessions',

        [

        'data'=>[

            'attributes'=>[


                'line_items'=>[

                    [

                    'currency'=>'PHP',

                    'amount'=>$amount * 100,

                    'name'=>'Enrollment Fee',

                    'quantity'=>1

                    ]

                ],



                'payment_method_types'=>[

                    'gcash'

                ],



                'success_url'=>
                'http://localhost:5173/student/payment/success',



                'cancel_url'=>
                'http://localhost:5173/student/payment/failed'


            ]

        ]

        ]);



        $data = $response->json();



        if(!$response->successful()){

            return response()->json([

                'message'=>'PayMongo checkout creation failed',

                'error'=>$data

            ],500);

        }



        $payment->update([

            'payment_reference'=>$data['data']['id']

        ]);



        return response()->json([

            'message'=>'Checkout session created successfully',

            'checkout_url'=>
            $data['data']['attributes']['checkout_url'],

            'payment_id'=>
            $payment->id,

            'enrollment_id'=>
            $enrollment->id

        ]);

        */







        /*
        |--------------------------------------------------------------------------
        | TEMPORARY PAYMENT SIMULATION
        | Remove after PayMongo verification
        |--------------------------------------------------------------------------
        */


        $payment->update([

            'payment_reference'=>
            'TEST-'.$payment->id

        ]);



        return response()->json([

            'message'=>'Test checkout created',

            'checkout_url'=>
            'http://localhost:5173/student/payment/success',

            'payment_id'=>
            $payment->id,

            'enrollment_id'=>
            $enrollment->id

        ]);

    }
    public function confirmPayment(Request $request)
{

    $student = $request->user()->student;


    $enrollment = $student
        ->enrollments()
        ->latest()
        ->first();



    if(!$enrollment){

        return response()->json([
            'message'=>'Enrollment not found'
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
            'message'=>'Payment record not found'
        ],404);

    }



    // simulate PayMongo webhook

    $payment->update([

        'status'=>'Paid'

    ]);



    $enrollment->update([

        'status'=>'Enrolled'

    ]);



    return response()->json([

        'message'=>'Payment successful',

        'payment'=>$payment,

        'enrollment'=>$enrollment

    ]);

}


}