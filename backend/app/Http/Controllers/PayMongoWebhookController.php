<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Enrollment;


class PayMongoWebhookController extends Controller
{


    public function handle(Request $request)
    {

        $payload = $request->all();


        \Log::info(
            'PayMongo Webhook:',
            $payload
        );


        /*
        Get event type
        */

        $event = $payload['data']['attributes']['type']
        ?? null;



        if(!$event){

            return response()->json([

                'message'=>'Invalid webhook'

            ],400);

        }



        /*
        Only process paid checkout
        */

        if($event !== 'checkout_session.payment.paid'){

            return response()->json([

                'message'=>'Event ignored'

            ]);

        }



        /*
        Get checkout session ID
        */

        $checkoutId =
        $payload['data']['attributes']['data']['id']
        ?? null;



        if(!$checkoutId){

            return response()->json([

                'message'=>'Checkout ID missing'

            ],400);

        }



        /*
        Find payment
        */

        $payment = Payment::where(

            'payment_reference',

            $checkoutId

        )->first();



        if(!$payment){

            return response()->json([

                'message'=>'Payment not found'

            ],404);

        }



        /*
        Update payment
        */

        $payment->update([

            'status'=>'Paid'

        ]);



        /*
        Update enrollment
        */

        Enrollment::where(

            'id',

            $payment->enrollment_id

        )->update([

            'status'=>'Enrolled'

        ]);




        return response()->json([

            'message'=>'Payment successfully confirmed'

        ]);

    }


}