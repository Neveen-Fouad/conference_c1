<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Services\GroqService;
use App\Services\CountryServices;
use App\Services\PlaceServices;
use App\Services\WeatherServices;
use Illuminate\Http\JsonResponse;

class AiTripController extends Controller
{
    public function __construct(
        protected GroqService $groqService,
        protected CountryServices $countryServices,
        protected PlaceServices $placeServices,
        protected WeatherServices $weatherServices
    ) {}

    public function generateTrip(StoreTripRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $city = $validated['destination']; 
        
        $weather = $this->weatherServices->getForecast($city); 
        $weatherForecast = $weather['forecast']['forecastday'] ?? []; 
     
        $attractions = $this->placeServices->getAttractions($city);
        $places = $attractions['results'] ?? [];


        $trip = $this->groqService->MakeTrip($validated, $city, $weatherForecast, $places);

        $decodedTrip = json_decode($trip, true);

        return response()->json($decodedTrip ?? 'error' );
    }
}