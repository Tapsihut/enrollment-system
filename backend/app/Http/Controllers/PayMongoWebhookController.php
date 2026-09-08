<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Payment;
use App\Models\Enrollment;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Get Payload
        |--------------------------------------------------------------------------
        */

        $payload = $request->all();


        Log::info(
            'PayMongo Webhook Received',
            $payload
        );


        /*
        |--------------------------------------------------------------------------
        | Get Event Type
        |--------------------------------------------------------------------------
        */

        $event =
            $payload['data']['attributes']['type']
            ?? null;


        if (!$event) {

            return response()->json([

                'message' => 'Invalid webhook.'

            ], 400);
        }


        /*
        |--------------------------------------------------------------------------
        | Only Process Successful Checkout
        |--------------------------------------------------------------------------
        |
        | PayMongo may send different payment-related events.
        |
        |--------------------------------------------------------------------------
        */

        if (
            $event !== 'checkout_session.payment.paid'
            &&
            $event !== 'payment.paid'
        ) {

            return response()->json([

                'message' => 'Event ignored.',

                'event' => $event

            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Resource ID
        |--------------------------------------------------------------------------
        */

        $resource =
            $payload['data']['attributes']['data']
            ?? null;


        if (!$resource) {

            return response()->json([

                'message' => 'Payment data missing.'

            ], 400);
        }


        $resourceId =
            $resource['id']
            ?? null;


        if (!$resourceId) {

            return response()->json([

                'message' => 'Payment reference missing.'

            ], 400);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Local Payment
        |--------------------------------------------------------------------------
        */

        $payment = Payment::where(

            'payment_reference',

            $resourceId

        )->first();


        /*
        |--------------------------------------------------------------------------
        | If This Is payment.paid Instead
        |--------------------------------------------------------------------------
        |
        | A payment.paid event contains a Payment ID, while our current
        | payment_reference stores the Checkout Session ID.
        |
        | Therefore, don't incorrectly mark another payment.
        |
        |--------------------------------------------------------------------------
        */

        if (!$payment && $event === 'payment.paid') {

            Log::warning(

                'PayMongo payment.paid received but no matching checkout session was found.',

                [
                    'paymongo_payment_id' => $resourceId
                ]

            );


            return response()->json([

                'message' =>
                    'Payment event received but local checkout was not matched.'

            ], 200);
        }


        if (!$payment) {

            return response()->json([

                'message' => 'Payment not found.'

            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Processing
        |--------------------------------------------------------------------------
        */

        if ($payment->status === 'Paid') {

            return response()->json([

                'message' =>
                    'Payment already processed.'

            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | Mark Payment Paid
        |--------------------------------------------------------------------------
        */

        $payment->update([

            'status' => 'Paid'

        ]);


        /*
        |--------------------------------------------------------------------------
        | Mark Enrollment Enrolled
        |--------------------------------------------------------------------------
        */

        Enrollment::where(

            'id',

            $payment->enrollment_id

        )->update([

            'status' => 'Enrolled'

        ]);


        /*
        |--------------------------------------------------------------------------
        | Log Successful Payment
        |--------------------------------------------------------------------------
        */

        Log::info(

            'Enrollment payment successfully confirmed.',

            [

                'payment_id' =>
                    $payment->id,

                'enrollment_id' =>
                    $payment->enrollment_id,

                'paymongo_reference' =>
                    $resourceId

            ]

        );


        return response()->json([

            'message' =>
                'Payment successfully confirmed.'

        ], 200);
    }
}