<?php

namespace App\Services;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Booking;

class PaymentService
{
    protected $paymentRepository;
    protected $fakeStripeService;
    protected $notificationService;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        FakeStripePaymentService $fakeStripeService,
        NotificationService $notificationService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->fakeStripeService = $fakeStripeService;
        $this->notificationService = $notificationService;
    }

    public function createBookingPayment($clientId, $bookingId)
    {
        $booking = Booking::where('id', $bookingId)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $stripeSession = $this->fakeStripeService
            ->createCheckoutSession();

        return $this->paymentRepository->create([
            'client_id' => $clientId,
            'booking_id' => $bookingId,
            'payment_type' => 'booking',
            'amount' => $booking->total_price,
            'currency' => $booking->currency,
            'status' => 'pending',
            'stripe_checkout_session_id' => $stripeSession['session_id'],
            'stripe_payment_intent_id' => $stripeSession['payment_intent_id'],
            'payment_method' => 'test_card',
        ]);
    }

    public function completePayment($paymentId)
    {
        $payment = $this->paymentRepository->update($paymentId, [
            'status' => 'paid',
            'paid_at' => now(),
            'failure_reason' => null,
        ]);

        if ($payment->booking_id) {
            Booking::where('id', $payment->booking_id)
                ->update([
                    'status' => 'confirmed',
                ]);
        }

        $this->notificationService
            ->sendPaymentSuccessNotification($payment->client);

        return $payment;
    }

    public function failPayment($paymentId)
    {
        $payment = $this->paymentRepository->update($paymentId, [
            'status' => 'failed',
            'failure_reason' => 'Test payment failed',
        ]);

        $this->notificationService
            ->sendPaymentFailedNotification($payment->client);

        return $payment;
    }

    public function getPayment($paymentId)
    {
        return $this->paymentRepository->findById($paymentId);
    }

    public function getClientPayments($clientId)
    {
        return $this->paymentRepository->getByClient($clientId);
    }
}
