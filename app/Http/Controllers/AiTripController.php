<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Services\GroqService;
use App\Services\CountryServices;
use App\Services\PlaceServices;
use App\Services\SearchService;
use App\Services\WeatherServices;
use Illuminate\Http\JsonResponse;

class AiTripController extends Controller
{
    public function __construct(
        protected GroqService $groqService,
        protected CountryServices $countryServices,
        protected PlaceServices $placeServices,
        protected WeatherServices $weatherServices,
        protected SearchService $searchService
    ) {}

    public function generateTrip(StoreTripRequest $request): JsonResponse
    {
        $validated = $request->validated();
      
        $hotelResult = $this->searchService->searchHotels($validated);
        
       $simplifiedHotels = collect($hotelResult['hotels'] ?? [])->take(5)->map(function ($h) {
        $nearbyItems = data_get($h, 'summary.nearbyPOIs.items', []);
        $nearbyPlaces = collect($nearbyItems)->pluck('text')->implode(', ');

    return [
        'name'     => data_get($h, 'summary.name', 'Unknown Hotel'),
        'fees'     => data_get($h, 'summary.fees') ?? 'Pricing not available',
        
        'address'  => data_get($h, 'summary.location.address.addressLine', 'Unknown Address'),
        'rating'   => data_get($h, 'reviewInfo.summary.overallScoreWithDescriptionA11y.value', 'No rating'),
        'tagline'  => data_get($h, 'summary.tagline', ''),
        'nearby'   => $nearbyPlaces ?: 'No nearby places listed', 
    ];
})->toArray();

      $weather = $this->weatherServices->getForecast($validated['destination']); 
        $rawForecast = $weather['forecast']['forecastday'] ?? []; 
     

        $checkIn = $validated['start_date'];
        $checkOut = $validated['end_date'];

        $weatherForecast = collect($rawForecast)
            ->filter(function ($day) use ($checkIn, $checkOut) {
                return $day['date'] >= $checkIn && $day['date'] <= $checkOut;
            })

            ->map(function ($day) {
                $hourlyData = collect($day['hour'] ?? [])->map(function ($hour) {
                    return [
                        'time' => substr($hour['time'], -5), 
                        'temp_c' => $hour['temp_c'] ?? '',
                        'condition' => data_get($hour, 'condition.text', '')
                    ];
                })->toArray();

                return [
                    'date' => $day['date'],
                    'daily_avg_temp_c' => data_get($day, 'day.avgtemp_c'),
                    'daily_condition' => data_get($day, 'day.condition.text'),
                    'hourly_forecast' => $hourlyData 
                ];
            })
            ->values() 
            ->toArray();

            if (empty($weatherForecast)) {
            $weatherForecast = "Weather forecast is unavailable for these dates because the trip is more than 14 days in the future. Assume standard seasonal weather for this destination.";
        }
     
        $trip = $this->groqService->MakeTrip($validated, $weatherForecast, $simplifiedHotels);


        return response()->json($trip);
    }
}