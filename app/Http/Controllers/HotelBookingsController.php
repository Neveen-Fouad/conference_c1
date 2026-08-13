<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookingRequest;
use App\Services\BookingService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class HotelBookingsController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected NotificationService $notificationService
    ) {}

    public function store(CreateBookingRequest $request)
    {
        try {

            $bookings = $this->bookingService->createBooking(auth('api')->user(), $request->validated());

            $this->notificationService->createNotification([
                'client_id' => $bookings->client_id,
                'type' => 'hotel_booking',
                'description' => 'Your hotel booking was created successfully.',
            ]);

            return response()->json($bookings, 201);

        } catch (\Exception $e) {
            Log::error('Error creating booking', ['exception' => $e]);

            return response()->json(['message' => 'Error creating booking.'], 500);
        }
    }
}
