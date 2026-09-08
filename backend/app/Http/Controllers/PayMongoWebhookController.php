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
        $payload = $request->all();

        Log::info('PayMongo Webhook Received', $payload);

        $event = $payload['data']['attributes']['type'] ?? null;

        if (!$event) {
            return response()->json([
                'message' => 'Invalid webhook.'
            ], 400);
        }

        if (
            $event !== 'checkout_session.payment.paid' &&
            $event !== 'payment.paid'
        ) {
            return response()->json([
                'message' => 'Event ignored.',
                'event' => $event
            ], 200);
        }

        $resource = $payload['data']['attributes']['data'] ?? null;

        if (!$resource) {
            return response()->json([
                'message' => 'Payment data missing.'
            ], 400);
        }

        $resourceId = $resource['id'] ?? null;
        $attributes = $resource['attributes'] ?? [];

        if (!$resourceId) {
            return response()->json([
                'message' => 'Payment reference missing.'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | payment.paid
        |--------------------------------------------------------------------------
        |
        | This event contains the actual PayMongo Payment ID:
        |
        | pay_XXXXXXXX
        |
        */

        if ($event === 'payment.paid') {

            $paymongoPaymentId = $resourceId;

            $metadata = $attributes['metadata'] ?? [];

            $localPaymentId =
                $metadata['local_payment_id'] ?? null;

            $enrollmentId =
                $metadata['enrollment_id'] ?? null;

            Log::info('PAYMONGO PAYMENT DATA', [
                'paymongo_payment_id' => $paymongoPaymentId,
                'local_payment_id' => $localPaymentId,
                'enrollment_id' => $enrollmentId
            ]);

            $payment = null;

            if ($localPaymentId) {
                $payment = Payment::find($localPaymentId);
            }

            if (!$payment && $paymongoPaymentId) {
                $payment = Payment::where(
                    'paymongo_payment_id',
                    $paymongoPaymentId
                )->latest()->first();
            }

            if (!$payment) {

                Log::warning(
                    'PAYMONGO PAYMENT NOT FOUND',
                    [
                        'paymongo_payment_id' => $paymongoPaymentId,
                        'local_payment_id' => $localPaymentId,
                        'enrollment_id' => $enrollmentId
                    ]
                );

                return response()->json([
                    'message' => 'Payment event received but local payment was not found.'
                ], 200);
            }

            if ($payment->status === 'Paid') {
                return response()->json([
                    'message' => 'Payment already processed.'
                ], 200);
            }

            $payment->update([
                'status' => 'Paid',
                'payment_method' => 'GCash',
                'paymongo_payment_id' => $paymongoPaymentId
            ]);

            Enrollment::where(
                'id',
                $payment->enrollment_id
            )->update([
                'status' => 'Enrolled'
            ]);

            Log::info(
                'Enrollment payment successfully confirmed.',
                [
                    'payment_id' =>
                        $payment->id,

                    'enrollment_id' =>
                        $payment->enrollment_id,

                    'paymongo_payment_id' =>
                        $paymongoPaymentId,

                    'paymongo_reference' =>
                        $payment->payment_reference
                ]
            );

            return response()->json([
                'message' => 'Payment successfully confirmed.'
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | checkout_session.payment.paid
        |--------------------------------------------------------------------------
        */

        $checkoutId = $resourceId;

        $metadata = $attributes['metadata'] ?? [];

        $localPaymentId =
            $metadata['local_payment_id'] ?? null;

        $enrollmentId =
            $metadata['enrollment_id'] ?? null;

        $payments =
            $attributes['payments'] ?? [];

        $paymongoPaymentId =
            $payments[0]['id'] ?? null;

        Log::info('PAYMONGO CHECKOUT PAYMENT DATA', [
            'checkout_id' => $checkoutId,
            'local_payment_id' => $localPaymentId,
            'enrollment_id' => $enrollmentId,
            'paymongo_payment_id' => $paymongoPaymentId
        ]);

        /*
        |--------------------------------------------------------------------------
        | Find Local Payment
        |--------------------------------------------------------------------------
        */

        $payment = null;

        if ($localPaymentId) {
            $payment = Payment::find($localPaymentId);
        }

        if (!$payment) {
            $payment = Payment::where(
                'payment_reference',
                $checkoutId
            )->latest()->first();
        }

        if (!$payment && $enrollmentId) {
            $payment = Payment::where(
                'enrollment_id',
                $enrollmentId
            )
            ->where('status', 'Pending')
            ->latest()
            ->first();
        }

        if (!$payment) {

            Log::warning(
                'PAYMONGO CHECKOUT PAYMENT NOT FOUND',
                [
                    'checkout_id' => $checkoutId,
                    'local_payment_id' => $localPaymentId,
                    'enrollment_id' => $enrollmentId,
                    'paymongo_payment_id' => $paymongoPaymentId
                ]
            );

            return response()->json([
                'message' => 'Payment not found.'
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Processing
        |--------------------------------------------------------------------------
        */

        if ($payment->status === 'Paid') {

            /*
            | Even if already Paid, make sure the PayMongo
            | payment ID is stored.
            */

            if (
                !$payment->paymongo_payment_id &&
                $paymongoPaymentId
            ) {
                $payment->update([
                    'paymongo_payment_id' =>
                        $paymongoPaymentId
                ]);
            }

            return response()->json([
                'message' => 'Payment already processed.'
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | Mark Payment Paid
        |--------------------------------------------------------------------------
        */

        $payment->update([
            'status' => 'Paid',
            'payment_method' => 'GCash',
            'paymongo_payment_id' => $paymongoPaymentId
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
                    $checkoutId,

                'paymongo_payment_id' =>
                    $paymongoPaymentId
            ]
        );

        return response()->json([
            'message' =>
                'Payment successfully confirmed.'
        ], 200);
    }
}
