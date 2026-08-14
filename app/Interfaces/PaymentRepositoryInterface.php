<?php

namespace App\Interfaces;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByClient($clientId);

    public function findForClient(int $paymentId, int $clientId);

    public function getByBooking($bookingId);

    public function findByGatewayReference($reference);

    public function findByPaymentReference($reference);
}
