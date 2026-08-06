<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFlightBookingRequest;
use App\Services\BookingFlightService;
use Illuminate\Support\Facades\Log;

class FlightBookingController extends Controller
{
    public function __construct(protected BookingFlightService $bookingService)
    {
    }

    public function store(CreateFlightBookingRequest $request)
    {
        try{
            $booking = $this->bookingService->createBooking($request->validated());
            return response()->json($booking, 201);
        }catch(\Exception $e){
            Log::error('Error creating flight booking', ['exception' => $e]);
            return response()->json(['message' => 'Error creating booking.'], 500);
        }
    }
}