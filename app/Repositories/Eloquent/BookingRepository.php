<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Models\bookings;
use Illuminate\Support\Collection;

class BookingRepository implements BookingRepositoryInterface
{
    /*
     * Create a new booking.
     */
    public function create(array $data): bookings
    {
        return bookings::create($data);
    }

    /*
     * Update an existing booking.
     */
    public function update(bookings $booking, array $data): bool
    {
        return $booking->update($data);
    }

    /*
     * Find booking by id.
     */
    public function find(int $id): ?bookings
    {
        return bookings::find($id);
    }


    /*
     * Get all bookings for a user.
     */
    public function findByUser(int $clientID): Collection
    {
        return bookings::where('client_id', $clientID)
            ->latest()
            ->get();
    }

    /*
     * Delete booking.
     */
    public function delete(bookings $booking): bool
    {
        return $booking->delete();
    }
}