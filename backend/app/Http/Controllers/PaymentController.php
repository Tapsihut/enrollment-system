<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class PaymentController extends Controller
{

    public function create(Request $request)
    {

        try {


            $user = auth()->user();


            if(!$user){

                return response()->json([
                    'message'=>'Unauthenticated'
                ],401);

            }



            /*
            Temporary student id
            Replace later when student relationship is ready
            */

            $studentId = $user->id;



            $payment = Payment::create([

                'student_id'=>$studentId,

                'reference_number'=>'ENR-'.time(),

                'amount'=>50000,

                'gateway'=>'PayMongo',

                'payment_method'=>'GCash',

                'status'=>'PENDING'

            ]);





            $response = Http::withBasicAuth(

                env('PAYMONGO_SECRET_KEY'),

                ''

            )->post(

                'https://api.paymongo.com/v1/checkout_sessions',

                [

                    "data"=>[

                        "attributes"=>[


                            "line_items"=>[

                                [

                                    "currency"=>"PHP",

                                    "amount"=>50000,

                                    "name"=>"Enrollment Fee",

                                    "quantity"=>1

                                ]

                            ],


                            "payment_method_types"=>[

                                "gcash"

                            ],


                        'success_url' => 'http://192.168.1.3:5173/student/payment/success',
                        'cancel_url' => 'http://192.168.1.3:5173/student/payment/failed'



                        ]

                    ]

                ]

            );




            if($response->failed()){


                return response()->json([

                    "message"=>"PayMongo Error",

                    "error"=>$response->json()

                ],500);


            }




            $checkout = $response->json();




            $checkoutId = 
            $checkout['data']['id'];



            $checkoutUrl =
            $checkout['data']['attributes']['checkout_url'];




            $payment->update([

                'checkout_id'=>$checkoutId

            ]);





            return response()->json([


                "checkout_url"=>$checkoutUrl


            ]);




        }

        catch(\Exception $e){



            Log::error($e->getMessage());



            return response()->json([


                "message"=>"Payment creation failed",

                "error"=>$e->getMessage()


            ],500);



        }



    }

    public function paymentInfo(Request $request)
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    $studentId = $user->id;

    $payment = Payment::where('student_id', $studentId)
        ->latest()
        ->first();

    $balance = 1500.00;
    $status = "UNPAID";

    if ($payment && strtoupper($payment->status) === "PAID") {

        $balance = 0.00;

        $status = "PAID";

    }

    return response()->json([

        'reference_number' => $payment->reference_number ?? null,

        'payment_status' => $status,

        'enrollment_fee' => $balance,

        'amount_paid' => $payment && strtoupper($payment->status) === "PAID"
            ? 1500.00
            : 0.00,

    ]);
}


}