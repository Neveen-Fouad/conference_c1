<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Collection;

class BookingRepository implements BookingRepositoryInterface
{
    /*
     * Create a new booking.
     */
    public function create(array $data): Booking
    {
        return Booking::create($data);
    }

    /*
     * Update an existing booking.
     */
    public function update(Booking $booking, array $data): bool
    {
        return $booking->update($data);
    }

    /*
     * Find booking by id.
     */
    public function find(int $id): ?Booking
    {
        return Booking::find($id);
    }

    /*
     * Get all bookings for a user.
     */
    public function findByUser(int $clientID): Collection
    {
        return Booking::where('client_id', $clientID)
            ->latest()
            ->get();
    }

    /*
     * Delete booking.
     */
    public function delete(Booking $booking): bool
    {
        return $booking->delete();
    }
}
