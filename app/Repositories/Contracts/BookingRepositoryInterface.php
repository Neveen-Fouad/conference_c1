<?php

namespace App\Repositories\Contracts;

use App\Models\Booking;
use Illuminate\Support\Collection;

interface BookingRepositoryInterface
{
    public function create(array $data): Booking;

    public function update(Booking $booking, array $data): bool;

    public function find(int $id): ?Booking;

    public function findByUser(int $userId): Collection;

    public function delete(Booking $booking): bool;
}
