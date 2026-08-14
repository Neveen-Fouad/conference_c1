<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookFlightRequest;
use App\Services\BookingFlightService;
use App\Services\NotificationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class FlightBookingController extends Controller
{
    public function __construct(
        protected BookingFlightService $bookingFlightService,
        protected NotificationService $notificationService
    ) {}

    public function bookFlight(BookFlightRequest $request)
    {
        try {
            $booking = $this->bookingFlightService->createBooking(
                auth('api')->user(),
                $request->validated()
            );

            $this->notificationService->createNotification([
                'client_id' => $booking->client_id,
                'type' => 'flight_booking',
                'description' => "Your flight booking #{$booking->id} was created successfully.",
            ]);

            return response()->json([
                'status' => true,
                'data' => $booking,
            ], 201);

        } catch (QueryException $e) {
            Log::error('Database error creating flight booking', ['exception' => $e]);

            return response()->json(['message' => 'Something went wrong saving your booking.'], 500);

        } catch (\Exception $e) {
            Log::warning('Flight booking rejected', ['message' => $e->getMessage()]);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
