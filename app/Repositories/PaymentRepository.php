<?php

namespace App\Repositories;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $payment)
    {
        parent::__construct($payment);
    }

    public function getByClient($clientId)
    {
        return $this->model
            ->where('client_id', $clientId)->latest()->get();
    }

    public function getByBooking($bookingId)
    {
        return $this->model->where('booking_id', $bookingId)->latest()->get();
    }

    public function findByGatewayReference($reference)
    {
        return $this->model->where('gateway_reference', $reference)->first();

    }
    public function findByPaymentReference($reference)
    {
        return $this->model->where('payment_reference', $reference)->first();
    }
}
