<?php

namespace App\Repositories\Contracts;

use App\Models\bookings;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{

    public function create(array $data): bookings;

    public function update(bookings $booking, array $data): bool;

    public function find(int $id): ?bookings;

    public function findByBookingNumber(string $bookingNumber): ?bookings;

    public function findByUser(int $userId): Collection;

    public function delete(bookings $booking): bool;
}