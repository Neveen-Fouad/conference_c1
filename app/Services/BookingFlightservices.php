<?php

namespace App\Services;

use App\Models\bookings;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingFlightService
{
    public function __construct(protected BookingRepositoryInterface $bookingRepository)
    {
    }

    public function createBooking(array $data): bookings
    {
        $payload = [
            'client_id' => Auth::id(),
            'type' => 'flight',
            'provider' => $data['provider'] ?? null,
            'external_reference_id' => $data['flight_id'],
            'number_of_days' => $this->calculateNumberOfDays($data),
            'check_in_date' => $data['departure_date'],
            'check_out_date' => $data['return_date'] ?? null,
            'booking_date' => Carbon::now(),
            'number_of_bookings' => $data['passengers'],
            'classes' => $data['cabin_class'] ?? null,
            'status' => 'pending',
            'total_price' => $data['total_price'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'details' => $data,
        ];

        return $this->bookingRepository->create($payload);
    }

    protected function calculateNumberOfDays(array $data): ?int
    {
        if (empty($data['return_date'])) {
            return null;
        }

        return Carbon::parse($data['departure_date'])->diffInDays(Carbon::parse($data['return_date']));
    }
}
