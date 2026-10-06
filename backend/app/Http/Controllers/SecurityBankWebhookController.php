<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityBankWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info(
            'SECURITY BANK WEBHOOK RECEIVED',
            $payload
        );

        /*
         * ---------------------------------------------------------
         * IMPORTANT
         * ---------------------------------------------------------
         *
         * The exact Security Bank callback fields must be changed
         * according to the bank's API documentation.
         */

        $status =
            data_get(
                $payload,
                'status'
            );

        $reference =
            data_get(
                $payload,
                'referenceNumber'
            );

        $transactionId =
            data_get(
                $payload,
                'transactionId'
            );

        $qrId =
            data_get(
                $payload,
                'qrId'
            );

        if (!$reference) {

            return response()->json([
                'message' =>
                    'Payment reference is missing.'
            ], 400);
        }

        $payment =
            Payment::where(
                'payment_reference',
                $reference
            )->latest()->first();

        if (!$payment && $transactionId) {

            $payment =
                Payment::where(
                    'security_bank_transaction_id',
                    $transactionId
                )->latest()->first();
        }

        if (!$payment) {

            Log::warning(
                'SECURITY BANK PAYMENT NOT FOUND',
                [
                    'reference' =>
                        $reference,

                    'transaction_id' =>
                        $transactionId,

                    'qr_id' =>
                        $qrId
                ]
            );

            return response()->json([
                'message' =>
                    'Payment not found.'
            ], 200);
        }

        /*
         * Idempotency:
         * If Security Bank sends the callback more than once,
         * don't process the payment again.
         */

        if ($payment->status === 'Paid') {

            return response()->json([
                'message' =>
                    'Payment already processed.'
            ], 200);
        }

        /*
         * The actual successful status must be replaced with
         * the value Security Bank sends.
         */

        if (
            in_array(
                strtoupper((string) $status),
                [
                    'PAID',
                    'SUCCESS',
                    'SUCCESSFUL',
                    'COMPLETED'
                ]
            )
        ) {

            $payment->update([
                'status' =>
                    'Paid',

                'payment_provider' =>
                    'Security Bank',

                'payment_method' =>
                    'QR',

                'security_bank_transaction_id' =>
                    $transactionId,

                'security_bank_qr_id' =>
                    $qrId
                    ??
                    $payment->security_bank_qr_id,
            ]);

            Enrollment::where(
                'id',
                $payment->enrollment_id
            )
                ->where(
                    'status',
                    'Pending'
                )
                ->update([
                    'status' =>
                        'Paid'
                ]);

            Log::info(
                'SECURITY BANK PAYMENT CONFIRMED',
                [
                    'payment_id' =>
                        $payment->id,

                    'enrollment_id' =>
                        $payment->enrollment_id,

                    'transaction_id' =>
                        $transactionId
                ]
            );

            return response()->json([
                'message' =>
                    'Payment successfully confirmed.'
            ], 200);
        }

        /*
         * Failed / cancelled payment.
         */

        if (
            in_array(
                strtoupper((string) $status),
                [
                    'FAILED',
                    'CANCELLED',
                    'CANCELED'
                ]
            )
        ) {

            $payment->update([
                'status' =>
                    'Failed',

                'security_bank_transaction_id' =>
                    $transactionId,

                'security_bank_qr_id' =>
                    $qrId
                    ??
                    $payment->security_bank_qr_id,
            ]);

            return response()->json([
                'message' =>
                    'Payment marked as failed.'
            ], 200);
        }

        return response()->json([
            'message' =>
                'Payment status received.'
        ], 200);
    }
}