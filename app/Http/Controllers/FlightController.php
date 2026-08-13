<?php

namespace App\Http\Controllers;

use App\Http\Requests\AirportSearchRequest;
use App\Http\Requests\FlightSearchRequest;
use App\Services\FlightService;

class FlightController extends Controller
{
    public function __construct(protected FlightService $flightService) {}

    public function searchAirports(AirportSearchRequest $request)
    {
        $results = $this->flightService->searchAirport($request->validated('query'));

        return response()->json(['status' => true, 'data' => $results]);
    }

    public function listFlights(FlightSearchRequest $request)
    {
        try {
            $result = $this->flightService->searchFlights($request->validated());
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Unable to search flights right now, please try again.',
            ], 502);
        }

        return response()->json([
            'status' => true,
            'sessionId' => $result['sessionId'],
            'data' => [
                'itineraries' => $result['itineraries'],
            ],
        ]);
    }
}
