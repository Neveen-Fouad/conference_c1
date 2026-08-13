<?php

namespace App\Repositories\Eloquent;

use App\Models\Booking;
use App\Repositories\Contracts\FlightsRepositoryInterface;
use Illuminate\Support\Collection;

class FlightsRepository implements FlightsRepositoryInterface
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
     * Find booking by booking number.
     */
    public function findByBookingNumber(string $bookingNumber): ?Booking
    {
        return Booking::where('booking_number', $bookingNumber)->first();
    }

    /*
     * Get all bookings for a user.
     */
    public function findByUser(int $userId): Collection
    {
        return Booking::whereHas('client', fn ($query) => $query->where('user_id', $userId))
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
