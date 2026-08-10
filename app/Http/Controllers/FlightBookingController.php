<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookFlightRequest;
use App\Services\BookingFlightService;
use Illuminate\Support\Facades\Log;

class FlightBookingController extends Controller
{
    public function __construct(protected BookingFlightService $bookingFlightService)
    {
    }

   public function bookFlight(BookFlightRequest $request)
{
    try {
        $booking = $this->bookingFlightService->createBooking(
            auth('api')->user(),
            $request->validated()
        );

        return response()->json([
            'status' => true,
            'data'   => $booking,
        ], 201);

    } catch (\Illuminate\Database\QueryException $e) {
        Log::error('Database error creating flight booking', ['exception' => $e]);
        return response()->json(['message' => 'Something went wrong saving your booking.'], 500);

    } catch (\Exception $e) {
        Log::warning('Flight booking rejected', ['message' => $e->getMessage()]);
        return response()->json(['message' => $e->getMessage()], 422);
    }
}
}