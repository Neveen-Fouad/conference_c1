<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class PaymobPaymentService
{
    public function createIntention(Payment $payment)
    {
        $client = $payment->client;

        $firstName = $client->user->first_name ?: 'Test';
        $lastName = $client->user->last_name ?: 'Customer';

        $amountCents = (int) round(
            $payment->amount * 100
        );

        $response = Http::withHeaders([
            'Authorization' => 'Token '.
                config('services.paymob.secret_key'),

            'Content-Type' => 'application/json',
        ])->post(
            config('services.paymob.base_url').
            '/v1/intention/',
            [
                'amount' => $amountCents,

                'currency' => strtoupper(
                    $payment->currency ?? 'EGP'
                ),

                'payment_methods' => [
                    (int) config(
                        'services.paymob.integration_id'
                    ),
                ],

                'items' => [
                    [
                        'name' => 'Booking #'.$payment->booking_id,

                        'amount' => $amountCents,

                        'description' => 'Travel booking payment',

                        'quantity' => 1,
                    ],
                ],

                'billing_data' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $client->email,

                    'phone_number' => $client->phone ?: '+201000000000',

                    'apartment' => 'NA',
                    'floor' => 'NA',
                    'street' => 'NA',
                    'building' => 'NA',
                    'shipping_method' => 'NA',
                    'postal_code' => 'NA',
                    'city' => 'Cairo',
                    'country' => 'EG',
                    'state' => 'Cairo',
                ],

                'customer' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $client->email,
                ],

                'special_reference' => $payment->payment_reference,

                'notification_url' => config(
                    'services.paymob.notification_url'
                ),

                'redirection_url' => config(
                    'services.paymob.redirection_url'
                ),

                'extras' => [
                    'payment_id' => $payment->id,
                    'booking_id' => $payment->booking_id,
                ],
            ]
        );

        if ($response->failed()) {
            throw new \Exception(
                'Paymob Error: '.$response->body()
            );
        }

        $data = $response->json();

        $checkoutUrl =
            config('services.paymob.base_url').
            '/unifiedcheckout/?publicKey='.
            urlencode(config('services.paymob.public_key')).
            '&clientSecret='.
            urlencode($data['client_secret']);

        return [
            'gateway_reference' => $data['id'],
            'checkout_url' => $checkoutUrl,
            'gateway_response' => $data,
        ];
    }
}
