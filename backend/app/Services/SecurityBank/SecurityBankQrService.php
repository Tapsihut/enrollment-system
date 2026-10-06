<?php

namespace App\Services\SecurityBank;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SecurityBankQrService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.security_bank.base_url'),
            '/'
        );
    }

    /**
     * Generate a Security Bank QR payment.
     *
     * IMPORTANT:
     * The exact endpoint and request fields must match
     * the API specification provided by Security Bank.
     */
    public function generateQr(Payment $payment): array
    {
        $reference = $payment->payment_reference;

        if (!$reference) {
            $reference = 'SFXC-' .
                now()->format('YmdHis') .
                '-' .
                $payment->id;

            $payment->update([
                'payment_reference' => $reference
            ]);
        }

        /*
         * ---------------------------------------------------------
         * SECURITY BANK API REQUEST
         * ---------------------------------------------------------
         *
         * Replace the endpoint and request structure below
         * with the exact Security Bank API documentation.
         */

        $endpoint = $this->baseUrl . '/qr';

        $payload = [
            'merchantId' => config(
                'services.security_bank.merchant_id'
            ),

            'referenceNumber' => $reference,

            'amount' => number_format(
                (float) $payment->amount,
                2,
                '.',
                ''
            ),

            'currency' => 'PHP',

            'description' => 'SFXC Enrollment Fee',

            'callbackUrl' => config(
                'services.security_bank.callback_url'
            ),
        ];

        try {

            $response = Http::timeout(30)
                ->acceptJson()
                ->withHeaders([
                    'X-API-KEY' => config(
                        'services.security_bank.api_key'
                    ),
                ])
                ->post(
                    $endpoint,
                    $payload
                );

        } catch (\Throwable $e) {

            Log::error(
                'SECURITY BANK QR CONNECTION ERROR',
                [
                    'payment_id' => $payment->id,
                    'message' => $e->getMessage(),
                ]
            );

            throw new RuntimeException(
                'Unable to connect to Security Bank.'
            );
        }

        if (!$response->successful()) {

            Log::error(
                'SECURITY BANK QR API ERROR',
                [
                    'payment_id' => $payment->id,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]
            );

            throw new RuntimeException(
                'Security Bank QR generation failed.'
            );
        }

        $data = $response->json();

        /*
         * These response fields must also be adjusted to match
         * Security Bank's actual API response.
         */

        $payment->update([
            'payment_provider' => 'Security Bank',

            'payment_method' => 'QR',

            'security_bank_payment_id' =>
                data_get($data, 'paymentId'),

            'security_bank_qr_id' =>
                data_get($data, 'qrId'),

            'security_bank_qr_data' =>
                data_get($data, 'qrData')
                ??
                data_get($data, 'qrCode')
                ??
                data_get($data, 'qrUrl'),
        ]);

        return [
            'success' => true,

            'payment_id' =>
                $payment->id,

            'reference' =>
                $reference,

            'qr_id' =>
                $payment->security_bank_qr_id,

            'qr_data' =>
                $payment->security_bank_qr_data,

            'response' =>
                $data,
        ];
    }
}