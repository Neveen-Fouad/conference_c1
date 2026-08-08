<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use App\Http\Requests\HotelSearchRequest;
use App\Services\FlightService;
use App\Http\Requests\AirportSearchRequest;
class SearchController extends Controller
{
    public function __construct(protected SearchService $searchService , protected FlightService $flightService)
    {
    }

    public function searchHotels(HotelSearchRequest $request)
    {
        $hotels = $this->searchService->searchHotels($request->validated());
        
        if (empty($hotels)) {
            return response()->json([
                'message' => 'No hotels found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'data' => $hotels
        ]);
    }
    public function searchAirports(AirportSearchRequest $request)
    {
        $airports = $this->flightService->searchAirports($request->validated());
        
        if (empty($airports)) {
            return response()->json([
                'message' => 'No airports found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'data' => $airports
        ]);
    }

}
