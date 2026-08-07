<?php

namespace App\Services;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Bookings;
use Illuminate\Support\Str;

class PaymentService
{
    protected $paymentRepository;
    protected $paymobPaymentService;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        PaymobPaymentService $paymobPaymentService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymobPaymentService = $paymobPaymentService;
    }

    public function createBookingPayment($clientId, $bookingId)
    {
        $booking = Bookings::where('id', $bookingId)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $payment = $this->paymentRepository->create([
            'client_id' => $clientId,
            'booking_id' => $bookingId,
            'payment_reference' =>
                'PAY-' . Str::upper(Str::random(12)),
            'payment_type' => 'booking',
            'amount' => $booking->total_price,
            'currency' => $booking->currency ?? 'EGP',
            'gateway' => 'paymob',
            'status' => 'pending',
            'payment_method' => 'card',
        ]);

        $paymobData = $this->paymobPaymentService
            ->createIntention($payment);

        $payment = $this->paymentRepository->update(
            $payment->id,
            [
                'gateway_reference' =>
                    $paymobData['gateway_reference'],
                'gateway_response' =>
                    $paymobData['gateway_response'],
            ]
        );

        return [
            'payment' => $payment,
            'checkout_url' => $paymobData['checkout_url'],
        ];
    }

    public function getPayment($paymentId)
    {
        return $this->paymentRepository
            ->findById($paymentId);
    }

    public function getClientPayments($clientId)
    {
        return $this->paymentRepository
            ->getByClient($clientId);
    }
}
