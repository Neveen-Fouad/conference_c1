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
    $itinerary = $data['itinerary'];

    $number_of_days = $this->calculateNumberOfDays($itinerary);

   $booking = [
    'client_id'             => $user->client->id,
    'type'                  => 'flight',
    'booking_type'          => 'flight',                                    // <-- ADD
    'provider'              => $itinerary['legs'][0]['carriers'][0]['name'],
    'provider_name'         => $itinerary['legs'][0]['carriers'][0]['name'], // <-- ADD
    'external_reference_id' => $itinerary['id'],
    'number_of_days'        => $number_of_days,
    'number_of_bookings'    => $data['adults'],
    'status'                => 'pending',
    'check_in_date'         => $itinerary['departure'],
    'check_out_date'        => $itinerary['arrival'],
    'booking_date'          => now()->toDateString(),
    'total_price'           => round($itinerary['price']['amount'], 2),
    'currency'              => $data['currency'] ?? 'USD',
    'classes'               => $this->cabinToTier($data['cabin_class']),
    'details' => [
        'flight' => [
            'id'          => $itinerary['id'],
            'cabin_class' => $data['cabin_class'],
        ],
        'guest' => [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'phone'      => $user->phone,
        ],
        'passengers' => $data['adults'],
    ],
];

    return $this->bookingRepository->create($booking);
}

protected function calculateNumberOfDays(array $itinerary): ?int
{
    if (empty($itinerary['arrival'])) {
        return null;
    }

    return Carbon::parse($itinerary['departure'])
        ->diffInDays(Carbon::parse($itinerary['arrival']));
}

protected function cabinToTier(string $cabinClass): string
{
    return match ($cabinClass) {
        'business', 'first' => 'luxury',
        'premium_economy'   => 'standard',
        default             => 'economy',
    };
}
}