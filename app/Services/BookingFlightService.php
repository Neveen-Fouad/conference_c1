<?php

namespace App\Services;

use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Models\User;
use Exception;
use Carbon\Carbon;

class BookingFlightService
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository,
        protected FlightService $flightService
    ) {
    }

    /*
     * Create a flight booking.
     */
    public function createBooking(User $user, array $data)
    {
        $flight = $this->flightService->getFlightDetails($data['flight_id']);

        if (!$flight) {
            throw new Exception('Flight not found.');
        }

        $total_price = data_get($flight, 'data.price.raw');
        $currency = data_get($flight, 'data.price.currency', 'USD');

        if ($total_price === null) {
            throw new Exception('No price available for the selected flight.');
        }

        $number_of_days = $this->calculateNumberOfDays($data);

        $booking = [
            'client_id' => $user->client->id,
            'type' => 'flight',
            'provider' => 'sky_scrapper',
            'external_reference_id' => $data['flight_id'],
            'number_of_days' => $number_of_days,
            'number_of_bookings' => $data['passengers'],
            'status' => 'pending',
            'check_in_date' => $data['departure_date'],
            'check_out_date' => $data['return_date'] ?? null,
            'booking_date' => now()->toDateString(),
            'total_price' => round($total_price, 2),
            'currency' => $currency,
            'details' => [
                'flight' => [
                    'id' => $data['flight_id'],
                    'origin' => data_get($flight, 'data.origin'),
                    'destination' => data_get($flight, 'data.destination'),
                    'cabin_class' => $data['cabin_class'] ?? null,
                ],
                'guest' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'passengers' => $data['passengers'],
            ],
        ];

        return $this->bookingRepository->create($booking);
    }

    /*
     * Confirm booking.
     */
    public function confirmBooking(int $bookingId): bool
    {
        $booking = $this->bookingRepository->find($bookingId);

        if (!$booking) {
            throw new Exception('Booking not found.');
        }

        return $this->bookingRepository->update($booking, [
            'status' => 'confirmed',
        ]);
    }

    /*
     * Cancel booking.
     */
    public function cancelBooking(int $bookingId): bool
    {
        $booking = $this->bookingRepository->find($bookingId);

        if (!$booking) {
            throw new Exception('Booking not found.');
        }

        return $this->bookingRepository->update($booking, [
            'status' => 'canceled',
        ]);
    }

    /*
     * Get booking.
     */
    public function findBooking(int $bookingId)
    {
        return $this->bookingRepository->find($bookingId);
    }

    protected function calculateNumberOfDays(array $data): ?int
    {
        if (empty($data['return_date'])) {
            return null;
        }

        return Carbon::parse($data['check_out_date'] ?? $data['return_date'])
            ->diffInDays(Carbon::parse($data['departure_date']));
    }
}