<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
use App\Http\Requests\FlightDetailsRequest;
use App\Services\FlightService;
use Illuminate\Http\Request;
use Throwable;

class FlightController extends Controller
{
    public function __construct(protected FlightService $flightService)
    {
    }


    public function searchAirport(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string',
            ]);

            $airportData = $this->flightService->searchAirport($request->query('query'));

            if (!$airportData) {
                return response()->json([
                    'status' => false,
                    'message' => 'Airport not found.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Airport retrieved successfully.',
                'data' => $airportData,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while searching for the airport.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

 
    public function index(FlightSearchRequest $request)
    {
        try {
            $filters = $request->validated();

            $searchResult = $this->flightService->searchFlights($filters);

            if (isset($searchResult['status']) && $searchResult['status'] === false) {
                return response()->json([
                    'status' => false,
                    'message' => $searchResult['message'] ?? 'Failed to search flights.',
                    'details' => $searchResult,
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => 'Flights retrieved successfully.',
                'data' => $searchResult,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching flight search results.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function showDetails(FlightDetailsRequest $request)
    {
        try {

            $params = $request->validated();

            $details = $this->flightService->getFlightDetails($params);

            if (!$details) {
                return response()->json([
                    'status' => false,
                    'message' => 'Flight details not found or failed to fetch.',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Flight details retrieved successfully.',
                'data' => $details,
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching flight details.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}