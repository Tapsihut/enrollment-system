<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Enrollment;
use App\Models\Payment;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Payment Information
    |--------------------------------------------------------------------------
    */

    public function paymentInfo(Request $request)
    {
        $student = $request->user()->student;

        $enrollment = $student
            ->enrollments()
            ->whereIn('status', ['Approved', 'Enrolled'])
            ->latest()
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'No approved enrollment found'
            ], 404);
        }

        return response()->json([
            'enrollment_id' => $enrollment->id,
            'status' => $enrollment->status,
            'amount' => 1500
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Create PayMongo Checkout
    |--------------------------------------------------------------------------
    */

    public function createCheckout($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Security Check
        |--------------------------------------------------------------------------
        */

        if ($enrollment->status !== 'Approved') {
            return response()->json([
                'message' => 'This enrollment is not approved for payment.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Check Existing Pending/Paid Payment
        |--------------------------------------------------------------------------
        */

        $existingPayment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
        ->whereIn('status', ['Pending', 'Paid'])
        ->latest()
        ->first();


        if ($existingPayment) {

            if ($existingPayment->status === 'Paid') {

                return response()->json([
                    'message' => 'This enrollment has already been paid.',
                    'payment_id' => $existingPayment->id
                ], 422);

            }

            /*
            |--------------------------------------------------------------------------
            | Existing Pending Payment
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'message' => 'A payment checkout already exists.',
                'payment_id' => $existingPayment->id
            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | Enrollment Fee
        |--------------------------------------------------------------------------
        */

        $amount = 1500;


        /*
        |--------------------------------------------------------------------------
        | Create Local Payment Record
        |--------------------------------------------------------------------------
        */

        $payment = Payment::create([

            'enrollment_id' => $enrollment->id,

            'amount' => $amount,

            'status' => 'Pending',

            'payment_method' => 'GCash'

        ]);


        /*
        |--------------------------------------------------------------------------
        | Create PayMongo TEST Checkout Session
        |--------------------------------------------------------------------------
        */

        $response = Http::withBasicAuth(

            config('services.paymongo.secret'),

            ''

        )->post(

            'https://api.paymongo.com/v1/checkout_sessions',

            [

                'data' => [

                    'attributes' => [

                        /*
                        |--------------------------------------------------------------------------
                        | Enrollment Fee
                        |--------------------------------------------------------------------------
                        */

                        'line_items' => [

                            [

                                'currency' => 'PHP',

                                'amount' => $amount * 100,

                                'name' => 'SFXC Enrollment Fee',

                                'quantity' => 1

                            ]

                        ],


                        /*
                        |--------------------------------------------------------------------------
                        | Payment Method
                        |--------------------------------------------------------------------------
                        */

                        'payment_method_types' => [

                            'gcash'

                        ],


                        /*
                        |--------------------------------------------------------------------------
                        | Redirect URLs
                        |--------------------------------------------------------------------------
                        */

                        'success_url' =>
                            'http://localhost:5173/student/payment/success',

                        'cancel_url' =>
                            'http://localhost:5173/student/payment/failed'

                    ]

                ]

            ]

        );


        /*
        |--------------------------------------------------------------------------
        | Check PayMongo Response
        |--------------------------------------------------------------------------
        */

        if (!$response->successful()) {

            /*
            |--------------------------------------------------------------------------
            | Mark Local Payment Failed
            |--------------------------------------------------------------------------
            */

            $payment->update([

                'status' => 'Failed'

            ]);


            return response()->json([

                'message' => 'PayMongo checkout creation failed.',

                'error' => $response->json()

            ], 500);
        }


        /*
        |--------------------------------------------------------------------------
        | Get PayMongo Data
        |--------------------------------------------------------------------------
        */

        $data = $response->json();

        \Log::info('PAYMONGO CHECKOUT CREATED', [
            'response' => $data
        ]);

        $checkoutId =
            $data['data']['id']
            ?? null;


        $checkoutUrl =
            $data['data']['attributes']['checkout_url']
            ?? null;


        if (!$checkoutId || !$checkoutUrl) {

            $payment->update([

                'status' => 'Failed'

            ]);


            return response()->json([

                'message' =>
                    'PayMongo returned an invalid checkout response.',

                'response' => $data

            ], 500);
        }


        /*
        |--------------------------------------------------------------------------
        | Save PayMongo Checkout ID
        |--------------------------------------------------------------------------
        */

        $payment->update([

            'payment_reference' => $checkoutId

        ]);


        /*
        |--------------------------------------------------------------------------
        | Return Checkout URL
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'message' =>
                'PayMongo test checkout created successfully.',

            'checkout_url' =>
                $checkoutUrl,

            'payment_id' =>
                $payment->id,

            'enrollment_id' =>
                $enrollment->id

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Confirm Payment
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | This endpoint no longer marks a payment as Paid.
    |
    | PayMongo webhook will do that.
    |
    |--------------------------------------------------------------------------
    */

        public function confirmPayment(Request $request)
    {
        return response()->json([
            'message' =>
                'Payment confirmation is handled by PayMongo webhook.'
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | PayMongo Webhook
    |--------------------------------------------------------------------------
    */

    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info('PAYMONGO WEBHOOK RECEIVED', [
            'payload' => $payload
        ]);

        return response()->json([
            'message' => 'Webhook received successfully.'
        ], 200);
    }
}