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
    | PAYMENT INFO
    |--------------------------------------------------------------------------
    */

    public function paymentInfo(Request $request)
    {
        $student = $request->user()->student;

        $enrollment = $student->enrollments()
            ->whereIn('status', ['Approved', 'Enrolled'])
            ->latest()
            ->first();

        if (!$enrollment) {
            return response()->json([
                'message' => 'No approved enrollment found'
            ], 404);
        }

        $payment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
        ->latest()
        ->first();

        return response()->json([
            'enrollment_id' => $enrollment->id,
            'status' => $enrollment->status,
            'enrollment_status' => $enrollment->status,
            'payment_status' => $payment?->status ?? 'Pending',
            'amount' => $payment?->amount ?? 1500,
            'payment_method' => $payment?->payment_method ?? 'GCash',
            'payment_reference' => $payment?->payment_reference,
            'paymongo_payment_id' => $payment?->paymongo_payment_id
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE CHECKOUT
    |--------------------------------------------------------------------------
    */

    public function createCheckout($id)
    {
        $enrollment = Enrollment::findOrFail($id);

        if ($enrollment->status !== 'Approved') {
            return response()->json([
                'message' =>
                    'This enrollment is not approved for payment.'
            ], 422);
        }

        $amount = 1500;

        /*
        | Check if already paid
        */

        $paidPayment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
        ->where('status', 'Paid')
        ->latest()
        ->first();

        if ($paidPayment) {
            return response()->json([
                'message' =>
                    'This enrollment has already been paid.',
                'payment_id' =>
                    $paidPayment->id
            ], 422);
        }

        /*
        | Get previous payment attempt
        */

        $previousPayment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
        ->latest()
        ->first();

        /*
        | Cancel previous pending attempt
        */

        if (
            $previousPayment &&
            $previousPayment->status === 'Pending'
        ) {
            $previousPayment->update([
                'status' => 'Failed'
            ]);
        }

        /*
        | Create new local payment
        */

        $payment = Payment::create([
            'enrollment_id' => $enrollment->id,
            'amount' => $amount,
            'status' => 'Pending',
            'payment_method' => 'GCash'
        ]);

        /*
        | PayMongo secret
        */

        $secret = config('services.paymongo.secret');

        if (!$secret) {

            Log::error(
                'PAYMONGO SECRET KEY IS MISSING'
            );

            $payment->update([
                'status' => 'Failed'
            ]);

            return response()->json([
                'message' =>
                    'PayMongo secret key is not configured.'
            ], 500);
        }

        /*
        | Create PayMongo Checkout
        */

        try {

            $response = Http::withBasicAuth(
                $secret,
                ''
            )->post(
                'https://api.paymongo.com/v1/checkout_sessions',
                [
                    'data' => [
                        'attributes' => [

                            'line_items' => [
                                [
                                    'currency' => 'PHP',
                                    'amount' =>
                                        $amount * 100,
                                    'name' =>
                                        'SFXC Enrollment Fee',
                                    'quantity' => 1
                                ]
                            ],

                            'payment_method_types' => [
                                'gcash'
                            ],

                            'description' =>
                                'SFXC Enrollment Fee',

                            /*
                            | IMPORTANT
                            |
                            | This metadata allows the webhook
                            | to identify the exact local payment.
                            */

                            'metadata' => [
                                'local_payment_id' =>
                                    (string) $payment->id,

                                'enrollment_id' =>
                                    (string) $enrollment->id
                            ],

                            'success_url' =>
                                'http://localhost:5173/student/payment/success',

                            'cancel_url' =>
                                'http://localhost:5173/student/payment/failed'
                        ]
                    ]
                ]
            );

        } catch (\Throwable $e) {

            Log::error(
                'PAYMONGO CHECKOUT EXCEPTION',
                [
                    'message' =>
                        $e->getMessage(),

                    'payment_id' =>
                        $payment->id,

                    'enrollment_id' =>
                        $enrollment->id
                ]
            );

            $payment->update([
                'status' => 'Failed'
            ]);

            return response()->json([
                'message' =>
                    'Unable to connect to PayMongo.',
                'error' =>
                    $e->getMessage()
            ], 500);
        }

        /*
        | PayMongo returned error
        */

        if (!$response->successful()) {

            Log::error(
                'PAYMONGO CHECKOUT FAILED',
                [
                    'status' =>
                        $response->status(),

                    'response' =>
                        $response->json()
                ]
            );

            $payment->update([
                'status' => 'Failed'
            ]);

            return response()->json([
                'message' =>
                    'PayMongo checkout creation failed.',
                'error' =>
                    $response->json()
            ], 500);
        }

        $data = $response->json();

        /*
        | Checkout Session ID
        */

        $checkoutId =
            $data['data']['id']
            ?? null;

        /*
        | Checkout URL
        */

        $checkoutUrl =
            $data['data']['attributes']['checkout_url']
            ?? null;

        if (
            !$checkoutId ||
            !$checkoutUrl
        ) {

            Log::error(
                'PAYMONGO INVALID CHECKOUT RESPONSE',
                [
                    'response' => $data
                ]
            );

            $payment->update([
                'status' => 'Failed'
            ]);

            return response()->json([
                'message' =>
                    'PayMongo returned an invalid checkout response.',
                'response' =>
                    $data
            ], 500);
        }

        /*
        | Save Checkout Session ID
        */

        $payment->update([
            'payment_reference' =>
                $checkoutId
        ]);

        Log::info(
            'PAYMONGO CHECKOUT CREATED',
            [
                'local_payment_id' =>
                    $payment->id,

                'enrollment_id' =>
                    $enrollment->id,

                'checkout_session_id' =>
                    $checkoutId
            ]
        );

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
    | CONFIRM PAYMENT
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
    | PAYMONGO WEBHOOK
    |--------------------------------------------------------------------------
    */

    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info(
            'PayMongo Webhook Received',
            [
                'data' =>
                    $payload['data'] ?? null
            ]
        );

        /*
        | Get event type
        */

        $eventType =
            $payload['data']['attributes']['type']
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT SESSION PAYMENT PAID
        |--------------------------------------------------------------------------
        */

        if (
            $eventType ===
            'checkout_session.payment.paid'
        ) {

            $resource =
                $payload['data']['attributes']['data']
                ?? [];

            $checkoutId =
                $resource['id']
                ?? null;

            $attributes =
                $resource['attributes']
                ?? [];

            /*
            | Get metadata
            */

            $metadata =
                $attributes['metadata']
                ?? [];

            $localPaymentId =
                $metadata['local_payment_id']
                ?? null;

            $enrollmentId =
                $metadata['enrollment_id']
                ?? null;

            /*
            | Get actual PayMongo Payment ID
            |
            | From your real payload:
            |
            | payments[0].id
            */

            $paymongoPaymentId = null;

            if (
                isset($attributes['payments']) &&
                is_array($attributes['payments'])
            ) {

                foreach (
                    $attributes['payments']
                    as $paymongoPayment
                ) {

                    if (
                        isset(
                            $paymongoPayment['id']
                        )
                    ) {

                        $paymongoPaymentId =
                            $paymongoPayment['id'];

                        break;
                    }
                }
            }

            Log::info(
                'PAYMONGO CHECKOUT PAYMENT DATA',
                [
                    'checkout_id' =>
                        $checkoutId,

                    'local_payment_id' =>
                        $localPaymentId,

                    'enrollment_id' =>
                        $enrollmentId,

                    'paymongo_payment_id' =>
                        $paymongoPaymentId
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Find local payment
            |--------------------------------------------------------------------------
            */

            $payment = null;

            /*
            | FIRST:
            | Use local_payment_id from metadata
            */

            if ($localPaymentId) {

                $payment =
                    Payment::find(
                        $localPaymentId
                    );
            }

            /*
            | SECOND:
            | Use checkout session ID
            */

            if (!$payment && $checkoutId) {

                $payment =
                    Payment::where(
                        'payment_reference',
                        $checkoutId
                    )
                    ->latest()
                    ->first();
            }

            /*
            | THIRD:
            | Use enrollment ID
            */

            if (
                !$payment &&
                $enrollmentId
            ) {

                $payment =
                    Payment::where(
                        'enrollment_id',
                        $enrollmentId
                    )
                    ->where(
                        'status',
                        'Pending'
                    )
                    ->latest()
                    ->first();
            }

            /*
            | Payment not found
            */

            if (!$payment) {

                Log::warning(
                    'PAYMONGO CHECKOUT PAID - LOCAL PAYMENT NOT FOUND',
                    [
                        'checkout_id' =>
                            $checkoutId,

                        'local_payment_id' =>
                            $localPaymentId,

                        'enrollment_id' =>
                            $enrollmentId,

                        'paymongo_payment_id' =>
                            $paymongoPaymentId
                    ]
                );

                return response()->json([
                    'message' =>
                        'Webhook received but payment not found.'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Update payment
            |--------------------------------------------------------------------------
            */

            $updateData = [
                'status' =>
                    'Paid',

                'payment_method' =>
                    'GCash'
            ];

            /*
            | Save actual PayMongo Payment ID
            */

            if ($paymongoPaymentId) {

                $updateData[
                    'paymongo_payment_id'
                ] = $paymongoPaymentId;
            }

            $payment->update(
                $updateData
            );

            /*
            |--------------------------------------------------------------------------
            | Update enrollment
            |--------------------------------------------------------------------------
            */

            $enrollment =
                Enrollment::find(
                    $payment->enrollment_id
                );

            if ($enrollment) {

                $enrollment->update([
                    'status' =>
                        'Enrolled'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Success Log
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
                        $payment->payment_reference,

                    'paymongo_payment_id' =>
                        $payment->paymongo_payment_id
                ]
            );

            return response()->json([
                'message' =>
                    'Payment successfully marked as paid.',

                'payment_id' =>
                    $payment->id,

                'paymongo_payment_id' =>
                    $payment->paymongo_payment_id
            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT PAID
        |--------------------------------------------------------------------------
        */

        if (
            $eventType ===
            'payment.paid'
        ) {

            /*
            | The payment ID itself is:
            |
            | pay_xxxxxxxxx
            */

            $paymongoPaymentId =
                $payload['data']['attributes']['data']['id']
                ?? null;

            Log::info(
                'PAYMONGO PAYMENT.PAID RECEIVED',
                [
                    'paymongo_payment_id' =>
                        $paymongoPaymentId
                ]
            );

            /*
            | Try existing local payment first
            */

            $payment = null;

            if ($paymongoPaymentId) {

                $payment =
                    Payment::where(
                        'paymongo_payment_id',
                        $paymongoPaymentId
                    )
                    ->latest()
                    ->first();
            }

            /*
            | If not found, retrieve the PayMongo payment
            | to get metadata.
            */

            if (
                !$payment &&
                $paymongoPaymentId
            ) {

                $secret =
                    config(
                        'services.paymongo.secret'
                    );

                try {

                    $response =
                        Http::withBasicAuth(
                            $secret,
                            ''
                        )->get(
                            'https://api.paymongo.com/v1/payments/' .
                            $paymongoPaymentId
                        );

                    if (
                        $response->successful()
                    ) {

                        $paymongoData =
                            $response->json();

                        $paymongoAttributes =
                            $paymongoData['data']['attributes']
                            ?? [];

                        $metadata =
                            $paymongoAttributes['metadata']
                            ?? [];

                        $localPaymentId =
                            $metadata['local_payment_id']
                            ?? null;

                        $enrollmentId =
                            $metadata['enrollment_id']
                            ?? null;

                        /*
                        | Find by local payment ID
                        */

                        if ($localPaymentId) {

                            $payment =
                                Payment::find(
                                    $localPaymentId
                                );
                        }

                        /*
                        | Find by enrollment ID
                        */

                        if (
                            !$payment &&
                            $enrollmentId
                        ) {

                            $payment =
                                Payment::where(
                                    'enrollment_id',
                                    $enrollmentId
                                )
                                ->where(
                                    'status',
                                    'Pending'
                                )
                                ->latest()
                                ->first();
                        }
                    }

                } catch (\Throwable $e) {

                    Log::error(
                        'PAYMONGO PAYMENT LOOKUP FAILED',
                        [
                            'message' =>
                                $e->getMessage(),

                            'paymongo_payment_id' =>
                                $paymongoPaymentId
                        ]
                    );
                }
            }

            /*
            | If still not found
            */

            if (!$payment) {

                Log::warning(
                    'PAYMONGO PAYMENT.PAID - LOCAL PAYMENT NOT FOUND',
                    [
                        'paymongo_payment_id' =>
                            $paymongoPaymentId
                    ]
                );

                return response()->json([
                    'message' =>
                        'Webhook received but payment not found.'
                ], 200);
            }

            /*
            | Update local payment
            */

            $payment->update([
                'status' =>
                    'Paid',

                'payment_method' =>
                    'GCash',

                'paymongo_payment_id' =>
                    $paymongoPaymentId
            ]);

            /*
            | Update enrollment
            */

            $enrollment =
                Enrollment::find(
                    $payment->enrollment_id
                );

            if ($enrollment) {

                $enrollment->update([
                    'status' =>
                        'Enrolled'
                ]);
            }

            Log::info(
                'Enrollment payment successfully confirmed.',
                [
                    'payment_id' =>
                        $payment->id,

                    'enrollment_id' =>
                        $payment->enrollment_id,

                    'paymongo_reference' =>
                        $payment->payment_reference,

                    'paymongo_payment_id' =>
                        $payment->paymongo_payment_id
                ]
            );

            return response()->json([
                'message' =>
                    'Payment successfully marked as paid.'
            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT FAILED
        |--------------------------------------------------------------------------
        */

        if (
            $eventType ===
            'payment.failed'
        ) {

            $paymongoPaymentId =
                $payload['data']['attributes']['data']['id']
                ?? null;

            $payment = null;

            /*
            | Try PayMongo payment ID
            */

            if ($paymongoPaymentId) {

                $payment =
                    Payment::where(
                        'paymongo_payment_id',
                        $paymongoPaymentId
                    )
                    ->latest()
                    ->first();
            }

            /*
            | Retrieve PayMongo payment metadata
            */

            if (
                !$payment &&
                $paymongoPaymentId
            ) {

                $secret =
                    config(
                        'services.paymongo.secret'
                    );

                try {

                    $response =
                        Http::withBasicAuth(
                            $secret,
                            ''
                        )->get(
                            'https://api.paymongo.com/v1/payments/' .
                            $paymongoPaymentId
                        );

                    if (
                        $response->successful()
                    ) {

                        $data =
                            $response->json();

                        $metadata =
                            $data['data']['attributes']['metadata']
                            ?? [];

                        $localPaymentId =
                            $metadata['local_payment_id']
                            ?? null;

                        if ($localPaymentId) {

                            $payment =
                                Payment::find(
                                    $localPaymentId
                                );
                        }
                    }

                } catch (\Throwable $e) {

                    Log::error(
                        'PAYMONGO FAILED PAYMENT LOOKUP ERROR',
                        [
                            'message' =>
                                $e->getMessage(),

                            'paymongo_payment_id' =>
                                $paymongoPaymentId
                        ]
                    );
                }
            }

            /*
            | Payment not found
            */

            if (!$payment) {

                Log::warning(
                    'PAYMONGO PAYMENT.FAILED - LOCAL PAYMENT NOT FOUND',
                    [
                        'paymongo_payment_id' =>
                            $paymongoPaymentId
                    ]
                );

                return response()->json([
                    'message' =>
                        'Webhook received but payment not found.'
                ], 200);
            }

            /*
            | Mark failed
            */

            $payment->update([
                'status' =>
                    'Failed',

                'paymongo_payment_id' =>
                    $paymongoPaymentId
            ]);

            Log::info(
                'PAYMONGO PAYMENT MARKED FAILED',
                [
                    'payment_id' =>
                        $payment->id,

                    'enrollment_id' =>
                        $payment->enrollment_id,

                    'paymongo_payment_id' =>
                        $payment->paymongo_payment_id
                ]
            );

            return response()->json([
                'message' =>
                    'Payment marked as failed.'
            ], 200);
        }


        /*
        |--------------------------------------------------------------------------
        | OTHER EVENTS
        |--------------------------------------------------------------------------
        */

        Log::info(
            'PAYMONGO WEBHOOK IGNORED',
            [
                'event_type' =>
                    $eventType
            ]
        );

        return response()->json([
            'message' =>
                'Webhook received successfully.'
        ], 200);
    }
}

