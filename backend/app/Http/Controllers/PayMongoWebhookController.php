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

        Log::info('PAYMONGO WEBHOOK RECEIVED', [
            'payload' => $payload
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get event type
        |--------------------------------------------------------------------------
        */

        $event = $payload['data']['attributes']['type'] ?? null;

        if (!$event) {
            Log::warning('PAYMONGO WEBHOOK INVALID EVENT');

            return response()->json([
                'message' => 'Invalid webhook.'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Only process successful payment events
        |--------------------------------------------------------------------------
        */

        if (
            $event !== 'checkout_session.payment.paid' &&
            $event !== 'payment.paid'
        ) {
            Log::info('PAYMONGO EVENT IGNORED', [
                'event' => $event
            ]);

            return response()->json([
                'message' => 'Event ignored.',
                'event' => $event
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | Get resource
        |--------------------------------------------------------------------------
        */

        $resource = $payload['data']['attributes']['data'] ?? null;

        if (!$resource) {
            Log::warning('PAYMONGO WEBHOOK PAYMENT DATA MISSING');

            return response()->json([
                'message' => 'Payment data missing.'
            ], 400);
        }

        $resourceId = $resource['id'] ?? null;
        $attributes = $resource['attributes'] ?? [];

        if (!$resourceId) {
            Log::warning('PAYMONGO WEBHOOK RESOURCE ID MISSING');

            return response()->json([
                'message' => 'Payment reference missing.'
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------------------
        */

        $metadata = $attributes['metadata'] ?? [];

        $localPaymentId = $metadata['local_payment_id'] ?? null;
        $enrollmentId = $metadata['enrollment_id'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | payment.paid
        |--------------------------------------------------------------------------
        */

        if ($event === 'payment.paid') {

            $paymongoPaymentId = $resourceId;

            Log::info('PAYMONGO PAYMENT DATA', [
                'paymongo_payment_id' => $paymongoPaymentId,
                'local_payment_id' => $localPaymentId,
                'enrollment_id' => $enrollmentId
            ]);

            /*
            |--------------------------------------------------------------------------
            | Find local payment
            |--------------------------------------------------------------------------
            */

            $payment = null;

            /*
            | 1. Find using local payment ID
            */

            if ($localPaymentId) {
                $payment = Payment::find($localPaymentId);
            }

            /*
            | 2. Find using PayMongo payment ID
            */

            if (!$payment) {
                $payment = Payment::where(
                    'paymongo_payment_id',
                    $paymongoPaymentId
                )->latest()->first();
            }

            /*
            | 3. Find using enrollment ID
            */

            if (!$payment && $enrollmentId) {
                $payment = Payment::where(
                    'enrollment_id',
                    $enrollmentId
                )
                ->where('status', 'Pending')
                ->latest()
                ->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Payment not found
            |--------------------------------------------------------------------------
            */

            if (!$payment) {

                Log::warning('PAYMONGO PAYMENT NOT FOUND', [
                    'paymongo_payment_id' => $paymongoPaymentId,
                    'local_payment_id' => $localPaymentId,
                    'enrollment_id' => $enrollmentId
                ]);

                return response()->json([
                    'message' => 'Payment event received but local payment was not found.'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Already processed
            |--------------------------------------------------------------------------
            */

            if ($payment->status === 'Paid') {

                if (
                    !$payment->paymongo_payment_id &&
                    $paymongoPaymentId
                ) {
                    $payment->update([
                        'paymongo_payment_id' => $paymongoPaymentId
                    ]);
                }

                /*
                | Make sure enrollment is also Paid.
                */

                Enrollment::where(
                    'id',
                    $payment->enrollment_id
                )->update([
                    'status' => 'Paid'
                ]);

                Log::info('PAYMONGO PAYMENT ALREADY PROCESSED', [
                    'payment_id' => $payment->id,
                    'event' => $event
                ]);

                return response()->json([
                    'message' => 'Payment already processed.'
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Mark payment as Paid
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'status' => 'Paid',
                'payment_method' => 'GCash',
                'paymongo_payment_id' => $paymongoPaymentId
            ]);

            /*
            |--------------------------------------------------------------------------
            | Mark enrollment as Paid
            |--------------------------------------------------------------------------
            */

            Enrollment::where(
                'id',
                $payment->enrollment_id
            )->update([
                'status' => 'Paid'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Log success
            |--------------------------------------------------------------------------
            */

            Log::info('PAYMONGO PAYMENT MARKED PAID', [
                'payment_id' => $payment->id,
                'enrollment_id' => $payment->enrollment_id,
                'paymongo_payment_id' => $paymongoPaymentId,
                'event' => $event
            ]);

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

        $payments = $attributes['payments'] ?? [];

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
        | Find local payment
        |--------------------------------------------------------------------------
        */

        $payment = null;

        /*
        | 1. Find using local payment ID
        */

        if ($localPaymentId) {
            $payment = Payment::find($localPaymentId);
        }

        /*
        | 2. Find using checkout session ID
        */

        if (!$payment) {
            $payment = Payment::where(
                'payment_reference',
                $checkoutId
            )->latest()->first();
        }

        /*
        | 3. Find using enrollment ID
        */

        if (!$payment && $enrollmentId) {
            $payment = Payment::where(
                'enrollment_id',
                $enrollmentId
            )
            ->where('status', 'Pending')
            ->latest()
            ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Payment not found
        |--------------------------------------------------------------------------
        */

        if (!$payment) {

            Log::warning('PAYMONGO CHECKOUT PAYMENT NOT FOUND', [
                'checkout_id' => $checkoutId,
                'local_payment_id' => $localPaymentId,
                'enrollment_id' => $enrollmentId,
                'paymongo_payment_id' => $paymongoPaymentId
            ]);

            return response()->json([
                'message' => 'Payment not found.'
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | Already processed
        |--------------------------------------------------------------------------
        */

        if ($payment->status === 'Paid') {

            /*
            | Store PayMongo payment ID if it is not already stored.
            */

            if (
                !$payment->paymongo_payment_id &&
                $paymongoPaymentId
            ) {
                $payment->update([
                    'paymongo_payment_id' => $paymongoPaymentId
                ]);
            }

            /*
            | Make sure enrollment is also Paid.
            */

            Enrollment::where(
                'id',
                $payment->enrollment_id
            )->update([
                'status' => 'Paid'
            ]);

            Log::info('PAYMONGO PAYMENT ALREADY PROCESSED', [
                'payment_id' => $payment->id,
                'event' => $event
            ]);

            return response()->json([
                'message' => 'Payment already processed.'
            ], 200);
        }

        /*
        |--------------------------------------------------------------------------
        | Mark payment as Paid
        |--------------------------------------------------------------------------
        */

        $payment->update([
            'status' => 'Paid',
            'payment_method' => 'GCash',
            'paymongo_payment_id' => $paymongoPaymentId
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mark enrollment as Paid
        |--------------------------------------------------------------------------
        */

        Enrollment::where(
            'id',
            $payment->enrollment_id
        )->update([
            'status' => 'Paid'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Log successful payment
        |--------------------------------------------------------------------------
        */

        Log::info('PAYMONGO PAYMENT MARKED PAID', [
            'payment_id' => $payment->id,
            'enrollment_id' => $payment->enrollment_id,
            'paymongo_payment_id' => $paymongoPaymentId,
            'event' => $event
        ]);

        return response()->json([
            'message' => 'Payment successfully confirmed.'
        ], 200);
    }
}

