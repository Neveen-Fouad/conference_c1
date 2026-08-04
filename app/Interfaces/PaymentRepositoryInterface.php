<?php

namespace App\Interfaces;

interface PaymentRepositoryInterface
{
    public function getByClient($clientId);

    public function getByBooking($bookingId);

    public function findByStripeSessionId($sessionId);

}
