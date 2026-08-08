<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BookFlightRequest;
use App\Models\bookings;

class FlightBookingController extends Controller
{
    public function bookFlight(BookFlightRequest $request)
    {
        $itinerary = $request->validated('itinerary');

        $client = auth()->user()->client;

        $booking = bookings::create([
            'client_id'             => $client->id,
            'type'                  => 'flight',
            'number_of_days'        => null,
            'number_of_bookings'    => $request->validated('adults'),
            'status'                => 'pending',
            'provider'              => $itinerary['legs'][0]['carriers'][0]['name'],
            'external_reference_id' => $itinerary['id'],
            'check_in_date'         => $itinerary['departure'],
            'check_out_date'        => $itinerary['arrival'],
            'booking_date'          => now()->toDateString(),
            'classes'               => $this->cabinToTier(
                $request->validated('cabin_class')
            ),
            'total_price'           => $itinerary['price']['amount'],
            'currency'              => $request->validated('currency', 'USD'),
            'details'               => $itinerary,
        ]);

        return response()->json([
            'status' => true,
            'data' => $booking
        ], 201);
    }
    public function cabinToTier(string $cabinClass): string
    {
        return match ($cabinClass) {
            'business','first' => 'luxury',
            'premium_economy' => 'standard',
            default => 'economy',
        };
    }

}
