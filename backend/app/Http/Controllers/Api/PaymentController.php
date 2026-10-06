<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\SecurityBank\SecurityBankQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function paymentInfo(Request $request)
    {
        $user = $request->user();
        $student = $user?->student;

        if (!$student) {
            return response()->json([
                'profile_complete' => false,
                'enrollment_exists' => false,
                'can_pay' => false,
                'message' => 'Student profile not found.',
            ], 404);
        }

        $requiredProfileFields = [
            'first_name',
            'last_name',
            'birth_date',
            'gender',
            'civil_status',
            'nationality',
            'contact_number',
            'email',
            'address',
        ];

        $profileComplete = true;

        foreach ($requiredProfileFields as $field) {
            if (empty($student->{$field})) {
                $profileComplete = false;
                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Get latest enrollment
        |--------------------------------------------------------------------------
        */

        $enrollment = Enrollment::with([
            'course',
            'curriculum',
            'schoolYear',
            'semester',
        ])
        ->where('student_id', $student->id)
        ->latest('id')
        ->first();

        if (!$enrollment) {
            return response()->json([
                'profile_complete' => $profileComplete,
                'enrollment_exists' => false,
                'can_pay' => false,
                'payment' => null,
                'enrollment' => null,
                'message' => 'Please complete your enrollment first.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get latest payment for this enrollment
        |--------------------------------------------------------------------------
        |
        | Do not rely only on the Enrollment->payment relationship.
        | Query the payments table directly so the latest PayMongo payment
        | is always returned.
        |
        */

        $payment = Payment::where(
            'enrollment_id',
            $enrollment->id
        )
        ->latest('id')
        ->first();

        /*
        |--------------------------------------------------------------------------
        | Payment permission
        |--------------------------------------------------------------------------
        */

        $canPay =
            $profileComplete &&
            $enrollment->status === 'Pending' &&
            (!$payment || $payment->status !== 'Paid');

        /*
        |--------------------------------------------------------------------------
        | Log what the API is returning
        |--------------------------------------------------------------------------
        */

        Log::info('STUDENT PAYMENT INFO', [
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'enrollment_status' => $enrollment->status,
            'payment_id' => $payment?->id,
            'payment_status' => $payment?->status,
            'payment_amount' => $payment?->amount,
            'payment_method' => $payment?->payment_method,
            'payment_reference' => $payment?->payment_reference,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Return payment information
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'profile_complete' => $profileComplete,
            'enrollment_exists' => true,
            'can_pay' => $canPay,

            'enrollment' => [
                'id' => $enrollment->id,
                'status' => $enrollment->status,
                'course' => $enrollment->course?->name,
                'curriculum' => $enrollment->curriculum?->name,
                'school_year' => $enrollment->schoolYear?->name,
                'semester' => $enrollment->semester?->name,
                'year_level' => $enrollment->year_level,
            ],

            'payment' => [
                'id' => $payment?->id,
                'status' => $payment?->status ?? 'Pending',
                'amount' => $payment?->amount ?? 1500,
                'payment_method' => $payment?->payment_method,
                'payment_reference' => $payment?->payment_reference,
                'payment_provider' => $payment?->payment_provider,
                'paymongo_payment_id' => $payment?->paymongo_payment_id,
                'security_bank_payment_id' => $payment?->security_bank_payment_id,
                'security_bank_qr_id' => $payment?->security_bank_qr_id,
                'security_bank_transaction_id' => $payment?->security_bank_transaction_id,
            ],

            'rejection_reason' => $enrollment->rejection_reason,
            'rejected_at' => $enrollment->rejected_at,
        ]);
    }

    public function createSecurityBankQr(
        Request $request,
        $id,
        SecurityBankQrService $securityBank
    ) {
        try {
            $user = $request->user();
            $student = $user?->student;

            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found.',
                ], 404);
            }

            $requiredProfileFields = [
                'first_name',
                'last_name',
                'birth_date',
                'gender',
                'civil_status',
                'nationality',
                'contact_number',
                'email',
                'address',
            ];

            foreach ($requiredProfileFields as $field) {
                if (empty($student->{$field})) {
                    return response()->json([
                        'message' => 'Please complete your student profile before payment.',
                    ], 422);
                }
            }

            $enrollment = Enrollment::where('id', $id)
                ->where('student_id', $student->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'message' => 'Enrollment not found.',
                ], 404);
            }

            if ($enrollment->status !== 'Pending') {
                return response()->json([
                    'message' => 'This enrollment is not available for payment.',
                    'status' => $enrollment->status,
                ], 422);
            }

            $existingPayment = Payment::where(
                'enrollment_id',
                $enrollment->id
            )
            ->latest('id')
            ->first();

            if ($existingPayment && $existingPayment->status === 'Paid') {
                return response()->json([
                    'message' => 'This enrollment has already been paid.',
                ], 422);
            }

            if (
                !$existingPayment ||
                $existingPayment->status !== 'Pending'
            ) {
                $existingPayment = Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'payment_reference' => 'SFXC-' . now()->format('YmdHis') . '-' . $enrollment->id,
                    'amount' => 1500,
                    'status' => 'Pending',
                    'payment_method' => 'QR',
                    'payment_provider' => 'Security Bank',
                ]);
            } else {
                $existingPayment->update([
                    'payment_method' => 'QR',
                    'payment_provider' => 'Security Bank',
                ]);
            }

            $qr = $securityBank->createQr(
                $existingPayment,
                $enrollment,
                $student
            );

            $existingPayment->refresh();

            return response()->json([
                'message' => 'Security Bank QR generated successfully.',
                'payment_id' => $existingPayment->id,
                'enrollment_id' => $enrollment->id,
                'amount' => $existingPayment->amount,
                'payment_status' => $existingPayment->status,
                'qr' => $qr,
            ]);
        } catch (\Throwable $e) {
            Log::error('SECURITY BANK QR ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Unable to generate Security Bank QR.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createCheckout(Request $request, $id)
    {
        try {
            $user = $request->user();
            $student = $user?->student;

            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found.',
                ], 404);
            }

            $requiredProfileFields = [
                'first_name',
                'last_name',
                'birth_date',
                'gender',
                'civil_status',
                'nationality',
                'contact_number',
                'email',
                'address',
            ];

            foreach ($requiredProfileFields as $field) {
                if (empty($student->{$field})) {
                    return response()->json([
                        'message' => 'Please complete your student profile before payment.',
                    ], 422);
                }
            }

            $enrollment = Enrollment::where('id', $id)
                ->where('student_id', $student->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'message' => 'Enrollment not found.',
                ], 404);
            }

            if ($enrollment->status !== 'Pending') {
                return response()->json([
                    'message' => 'This enrollment is not available for payment.',
                    'status' => $enrollment->status,
                ], 422);
            }

            $amount = 1500;

            $existingPaidPayment = Payment::where(
                'enrollment_id',
                $enrollment->id
            )
            ->where('status', 'Paid')
            ->latest('id')
            ->first();

            if ($existingPaidPayment) {
                return response()->json([
                    'message' => 'This enrollment has already been paid.',
                ], 422);
            }

            Payment::where('enrollment_id', $enrollment->id)
                ->where('status', 'Pending')
                ->update([
                    'status' => 'Failed',
                ]);

            $secret = config('services.paymongo.secret');

            if (!$secret) {
                return response()->json([
                    'message' => 'PayMongo secret key is not configured.',
                ], 500);
            }

            $payment = Payment::create([
                'enrollment_id' => $enrollment->id,
                'payment_reference' => null,
                'amount' => $amount,
                'status' => 'Pending',
                'payment_method' => 'GCash',
                'payment_provider' => 'PayMongo',
            ]);

            $response = Http::withBasicAuth($secret, '')
                ->acceptJson()
                ->post(
                    'https://api.paymongo.com/v1/checkout_sessions',
                    [
                        'data' => [
                            'attributes' => [
                                'line_items' => [
                                    [
                                        'currency' => 'PHP',
                                        'amount' => $amount * 100,
                                        'name' => 'SFXC Enrollment Fee',
                                        'quantity' => 1,
                                    ],
                                ],

                                'payment_method_types' => [
                                    'gcash',
                                ],

                                'description' =>
                                    'St. Francis Xavier College Enrollment Fee',

                                'metadata' => [
                                    'local_payment_id' =>
                                        (string) $payment->id,

                                    'enrollment_id' =>
                                        (string) $enrollment->id,
                                ],

                                'success_url' =>
                                    'http://192.168.1.3:5173/student/payment/success',

                                'cancel_url' =>
                                    'http://192.168.1.3:5173/student/payment/failed',
                            ],
                        ],
                    ]
                );

            if (!$response->successful()) {
                $payment->update([
                    'status' => 'Failed',
                ]);

                Log::error('PAYMONGO CHECKOUT ERROR', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'message' => 'PayMongo checkout could not be created.',
                    'error' => $response->json(),
                ], 500);
            }

            $data = $response->json('data');

            $checkoutId = $data['id'] ?? null;
            $checkoutUrl = $data['attributes']['checkout_url'] ?? null;

            if (!$checkoutId || !$checkoutUrl) {
                $payment->update([
                    'status' => 'Failed',
                ]);

                Log::error('PAYMONGO CHECKOUT INVALID RESPONSE', [
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'message' => 'PayMongo returned an invalid checkout response.',
                ], 500);
            }

            $payment->update([
                'payment_reference' => $checkoutId,
            ]);

            Log::info('PAYMONGO CHECKOUT CREATED', [
                'local_payment_id' => $payment->id,
                'enrollment_id' => $enrollment->id,
                'checkout_session_id' => $checkoutId,
            ]);

            return response()->json([
                'message' => 'PayMongo checkout created.',
                'checkout_url' => $checkoutUrl,
                'payment_id' => $payment->id,
                'enrollment_id' => $enrollment->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('PAYMONGO CHECKOUT EXCEPTION', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'message' => 'Unable to create PayMongo checkout.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('PAYMONGO WEBHOOK RECEIVED', [
                'payload' => $payload,
            ]);

            $event = data_get(
                $payload,
                'data.attributes.type'
            );

            if (!in_array($event, [
                'checkout_session.payment.paid',
                'payment.paid',
            ])) {
                Log::info('PAYMONGO WEBHOOK IGNORED', [
                    'event' => $event,
                ]);

                return response()->json([
                    'message' => 'Event ignored.',
                    'event' => $event,
                ], 200);
            }

            $resource = data_get(
                $payload,
                'data.attributes.data'
            );

            $attributes = data_get(
                $resource,
                'attributes',
                []
            );

            $metadata = data_get(
                $attributes,
                'metadata',
                []
            );

            $localPaymentId =
                $metadata['local_payment_id'] ?? null;

            $enrollmentId =
                $metadata['enrollment_id'] ?? null;

            $paymongoPaymentId = null;
            $checkoutSessionId = null;

            if ($event === 'payment.paid') {
                $paymongoPaymentId = data_get(
                    $resource,
                    'id'
                );
            }

            if ($event === 'checkout_session.payment.paid') {
                $checkoutSessionId = data_get(
                    $resource,
                    'id'
                );

                $payments = data_get(
                    $attributes,
                    'payments',
                    []
                );

                if (!empty($payments)) {
                    $paymongoPaymentId =
                        $payments[0]['id'] ?? null;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Find payment
            |--------------------------------------------------------------------------
            */

            $payment = null;

            if ($localPaymentId) {
                $payment = Payment::find($localPaymentId);
            }

            if (!$payment && $paymongoPaymentId) {
                $payment = Payment::where(
                    'paymongo_payment_id',
                    $paymongoPaymentId
                )->first();
            }

            if (!$payment && $checkoutSessionId) {
                $payment = Payment::where(
                    'payment_reference',
                    $checkoutSessionId
                )->first();
            }

            if (!$payment && $enrollmentId) {
                $payment = Payment::where(
                    'enrollment_id',
                    $enrollmentId
                )
                ->where('status', 'Pending')
                ->latest('id')
                ->first();
            }

            if (!$payment) {
                Log::warning('PAYMONGO PAYMENT NOT FOUND', [
                    'event' => $event,
                    'local_payment_id' => $localPaymentId,
                    'enrollment_id' => $enrollmentId,
                    'paymongo_payment_id' => $paymongoPaymentId,
                    'checkout_session_id' => $checkoutSessionId,
                ]);

                return response()->json([
                    'message' => 'Payment record not found.',
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Update payment
            |--------------------------------------------------------------------------
            */

            $payment->update([
                'status' => 'Paid',
                'payment_method' => 'GCash',
                'payment_provider' => 'PayMongo',
                'paymongo_payment_id' => $paymongoPaymentId
                    ?: $payment->paymongo_payment_id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update enrollment
            |--------------------------------------------------------------------------
            */

            $enrollment = Enrollment::find(
                $payment->enrollment_id
            );

            if ($enrollment) {
                if ($enrollment->status === 'Pending') {
                    $enrollment->update([
                        'status' => 'Paid',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Log successful payment
            |--------------------------------------------------------------------------
            */

            Log::info('PAYMONGO PAYMENT MARKED PAID', [
                'payment_id' => $payment->id,
                'enrollment_id' => $enrollment?->id,
                'payment_status' => $payment->status,
                'enrollment_status' => $enrollment?->status,
                'paymongo_payment_id' => $payment->paymongo_payment_id,
                'event' => $event,
            ]);

            return response()->json([
                'message' => 'Payment successfully processed.',
            ], 200);

        } catch (\Throwable $e) {

            Log::error('PAYMONGO WEBHOOK ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed.',
            ], 500);
        }
    }

    public function confirmPayment()
    {
        return response()->json([
            'message' =>
                'Payment confirmation is handled by the payment webhook.',
        ]);
    }
}

