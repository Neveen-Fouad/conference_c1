<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\BookingRepositoryInterface;
use Carbon\Carbon;
use Exception;

class BookingService
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository,
        protected HotelService $hotelService
    ) {}

    /*
     * Create a hotel booking.
     */
    public function createBooking(User $user, array $data)
    {
        $hotel = $this->hotelService->getHotelDetails($data['hotel_id']);

        if (! $hotel) {
            throw new Exception('Hotel not found.');
        }

        $offers = $this->hotelService->getHotelOffers(
            $data['hotel_id'],
            $data['check_in_date'],
            $data['check_out_date'],
            $data['guests']
        );

        if (! $offers || empty($offers['rooms'])) {
            throw new Exception('No offers available for the selected dates.');
        }

        $room = null;
        $priceDetails = null;

        foreach ($offers['rooms'] as $r) {
            $rp = data_get($r, 'propertyUnit.ratePlans.0.priceDetails.0');
            $amount = data_get($rp, 'lodgingPrepareCheckout.totalPrice.amount');

            if ($amount !== null) {
                $room = $r;
                $priceDetails = $rp;
                break;
            }
        }

        if (! $room || ! $priceDetails) {
            throw new Exception('No bookable rooms available for the selected dates.');
        }

        $total_price = data_get($priceDetails, 'lodgingPrepareCheckout.totalPrice.amount');
        $currency = data_get($priceDetails, 'lodgingPrepareCheckout.totalPrice.currencyInfo.code', 'USD');
        $roomName = data_get($room, 'header.name');
        $checkIn = Carbon::parse($data['check_in_date']);
        $checkOut = Carbon::parse($data['check_out_date']);
        $number_of_days = (int) ceil($checkIn->diffInDays($checkOut));

        $booking = [
            'client_id' => $user->client->id,
            'type' => 'hotel',
            'booking_type' => 'hotel',
            'provider' => 'hotels_com',
            'provider_name' => 'hotels_com',
            'external_reference_id' => $data['hotel_id'],
            'number_of_days' => $number_of_days,
            'number_of_bookings' => $data['rooms'],
            'status' => 'pending',
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'booking_date' => now()->toDateString(),
            'total_price' => round($total_price, 2),
            'currency' => $currency,
            'details' => [
                'hotel' => [
                    'id' => $data['hotel_id'],
                    'name' => data_get($hotel, 'summary.name'),
                    'address' => data_get($hotel, 'summary.location.address'),
                ],
                'room' => [
                    'name' => $roomName,
                ],
                'guest' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->client->phone,
                ],
                'rooms' => $data['rooms'],
                'guests' => $data['guests'],
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

        if (! $booking) {
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

        if (! $booking) {
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
}
