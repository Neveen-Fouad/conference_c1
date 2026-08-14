<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Models\Client;
use App\Models\Trip;
use App\Repositories\Eloquent\TripRepository;
use App\Services\CountryServices;
use App\Services\GroqService;
use App\Services\HotelService;
use App\Services\NotificationService;
use App\Services\PlaceServices;
use App\Services\RestaurantService;
use App\Services\SearchService;
use App\Services\TransportationService;
use App\Services\WeatherServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AiTripController extends Controller
{
    public function __construct(
        protected GroqService $groqService,
        protected CountryServices $countryServices,
        protected PlaceServices $placeServices,
        protected WeatherServices $weatherServices,
        protected SearchService $searchService,
        protected TransportationService $transportationService,
        protected RestaurantService $restaurantService,
        protected NotificationService $notificationService,
        protected TripRepository $trips,
        protected HotelService $hotelService
    ) {}
 
    public function generateTrip(StoreTripRequest $request): JsonResponse
    {
        Gate::authorize('createViaAi', trip::class);
        $validated = $request->validated();
 
        $checkOutDate = \Carbon\Carbon::parse($validated['start_date'])->addDays($validated['number_of_days'] - 1)->toDateString();
 
        $hotelFilters = [
            'destination' => $validated['destination'],
            'budget'      => $validated['budget'],
            'check_in'    => $validated['start_date'],
            'check_out'   => $checkOutDate,
            'guests'      => $validated['number_of_travels'],
            'style'       => $validated['style'],
        ];
        $hotelResult = $this->searchService->searchHotels($hotelFilters);
 
        $simplifiedHotels = collect(data_get($hotelResult, 'hotels.properties', []))->take(5)->map(function ($h) {
            $nearbyPlaces = collect(data_get($h, 'messages', []))->implode(', ');
 
            return [
                'id'      => data_get($h, 'id'),
                'name'    => data_get($h, 'name', 'Unknown Hotel'),
                'fees'    => data_get($h, 'price.priceSummary.definition.displayPrice') ?? 'Pricing not available',
                'address' => data_get($h, 'messages.0', 'Unknown Address'),
                'rating'  => data_get($h, 'guestRating.rating', 'No rating'),
                'tagline' => collect(data_get($h, 'short_amenities', []))->implode(', '),
                'nearby'  => $nearbyPlaces ?: 'No nearby places listed',
            ];
        })->values();
 
        
        $simplifiedHotels = $simplifiedHotels
            ->sortByDesc(fn ($h) => is_numeric($h['rating']) ? (float) $h['rating'] : -1)
            ->values()
            ->toArray();

        if (!empty($simplifiedHotels) && !empty($simplifiedHotels[0]['id'])) {
            $details = $this->hotelService->getHotelDetails($simplifiedHotels[0]['id']);
            $simplifiedHotels[0]['lat'] = data_get($details, 'summary.location.coordinates.latitude');
            $simplifiedHotels[0]['lng'] = data_get($details, 'summary.location.coordinates.longitude');
        }
 
        $weather = $this->weatherServices->getForecast($validated['destination']);
        $rawForecast = $weather['forecast']['forecastday'] ?? [];
 
        $checkIn = $validated['start_date'];
        $checkOut = $checkOutDate;
 
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
 
        $attractionsResponse = $this->placeServices->getAttractions($validated['destination']);
        $rawAttractions = $attractionsResponse['results'] ?? $attractionsResponse ?? [];
 
        $simplifiedAttractions = collect($rawAttractions)->take(15)->map(function ($a) {
            return [
                'name' => data_get($a, 'name', 'Unknown Attraction'),
                'rating' => data_get($a, 'rating', 'No rating'),
                'description' => substr(data_get($a, 'description', 'No description'), 0, 150),
                'lat' => data_get($a, 'latitude') ?? data_get($a, 'lat'),
                'lng' => data_get($a, 'longitude') ?? data_get($a, 'lng'),
            ];
        })->toArray();
 
        try {
            $restaurantsResponse = $this->restaurantService->listRestaurants(['city' => $validated['destination'], 'page' => 1]);
            $rawRestaurants = $restaurantsResponse['data'] ?? $restaurantsResponse['results'] ?? $restaurantsResponse ?? [];
        } catch (\Throwable $e) {
            Log::warning('Restaurant fetch failed, continuing without restaurants', [
                'message' => $e->getMessage(),
            ]);
            $rawRestaurants = [];
        }
 
        $simplifiedRestaurants = collect($rawRestaurants)->take(10)->map(function ($r) {
            return [
                'name' => data_get($r, 'name', 'Unknown Restaurant'),
                'rating' => data_get($r, 'rating', 'No rating'),
                'lat' => data_get($r, 'coordinates.latitude') ?? data_get($r, 'latitude') ?? data_get($r, 'lat'),
                'lng' => data_get($r, 'coordinates.longitude') ?? data_get($r, 'longitude') ?? data_get($r, 'lng'),
                'price' => data_get($r, 'price_level') ?? data_get($r, 'price.price_range') ?? data_get($r, 'price') ?? 'Price not available',
            ];
        })->toArray();
 
       
        if (!empty($simplifiedHotels) && isset($simplifiedHotels[0]) && $simplifiedHotels[0]['lat'] && $simplifiedHotels[0]['lng']) {
 
            $origin = ['lat' => (float) $simplifiedHotels[0]['lat'], 'lng' => (float) $simplifiedHotels[0]['lng']];
 
            $attractionDestinations = collect($simplifiedAttractions)
                ->filter(fn ($a) => $a['lat'] !== null && $a['lng'] !== null)
                ->map(fn ($a) => ['id' => $a['name'], 'lat' => (float) $a['lat'], 'lng' => (float) $a['lng']])
                ->values()->toArray();
 
            $restaurantDestinations = collect($simplifiedRestaurants)
                ->filter(fn ($r) => $r['lat'] !== null && $r['lng'] !== null)
                ->map(fn ($r) => ['id' => $r['name'], 'lat' => (float) $r['lat'], 'lng' => (float) $r['lng']])
                ->values()->toArray();
 
            $allDestinations = array_merge($attractionDestinations, $restaurantDestinations);
 
            if (!empty($allDestinations)) {
                $travelTimes = $this->transportationService->getTravelTimes($origin, $allDestinations);
 
                foreach ($simplifiedAttractions as &$attraction) {
                    $key = $attraction['name'];
                    $attraction['travel_time_from_hotel'] = isset($travelTimes[$key]) ? $travelTimes[$key]['label'] : 'Unknown';
                }
                unset($attraction);
 
                foreach ($simplifiedRestaurants as &$restaurant) {
                    $key = $restaurant['name'];
                    $restaurant['travel_time_from_hotel'] = isset($travelTimes[$key]) ? $travelTimes[$key]['label'] : 'Unknown';
                }
                unset($restaurant);
            }
        }
 
        $recommendedHotel = $simplifiedHotels[0]['name'] ?? null;
 
        $tripData = $this->groqService->MakeTrip(
            $validated,
            $weatherForecast,
            $simplifiedHotels,
            $simplifiedAttractions,
            $simplifiedRestaurants,
            $recommendedHotel
        );
 
        
        if (!isset($tripData['trip']) || !is_array($tripData['trip'])) {
            Log::error('Groq trip response missing/invalid `trip` array', ['tripData' => $tripData]);
            throw new \RuntimeException('AI returned an incomplete trip plan. Please try again.');
        }
 
        $validHotelNames = collect($simplifiedHotels)->pluck('name')->all();
        if (!empty($validHotelNames) && !in_array($tripData['best_hotel'] ?? null, $validHotelNames, true)) {
            Log::warning('AI selected a hotel not present in search results', [
                'best_hotel' => $tripData['best_hotel'] ?? null,
                'valid_hotels' => $validHotelNames,
            ]);
        }
 
    
        $strictTotalCost = 0;
        $guestsCount = (int) ($validated['number_of_travels'] ?? 1);
 
        foreach ($tripData['trip'] as &$day) {
            $hotelPerNight = (float) ($day['hotel_per_night'] ?? 0);
            $perPersonMealsActivities = (float) ($day['activities_and_meals_cost_per_person'] ?? 0);
 
            $strictDailyCost = $hotelPerNight + ($perPersonMealsActivities * $guestsCount);
 
            $day['daily_cost'] = $strictDailyCost;
            $strictTotalCost += $strictDailyCost;
        }
        unset($day);
        $tripData['total_estimated_cost'] = $strictTotalCost;
 
    
        if ($tripData['total_estimated_cost'] > $validated['budget'] && $tripData['total_estimated_cost'] > 0) {
            $ratio = $validated['budget'] / $tripData['total_estimated_cost'];
            $scaledTotalCost = 0;
 
            foreach ($tripData['trip'] as &$day) {
                $day['hotel_per_night'] = floor(($day['hotel_per_night'] ?? 0) * $ratio);
                $day['activities_and_meals_cost_per_person'] = floor(($day['activities_and_meals_cost_per_person'] ?? 0) * $ratio);
 
                $scaledDailyCost = $day['hotel_per_night'] + ($day['activities_and_meals_cost_per_person'] * $guestsCount);
                $day['daily_cost'] = $scaledDailyCost;
                $scaledTotalCost += $scaledDailyCost;
            }
            unset($day);
            $tripData['total_estimated_cost'] = $scaledTotalCost;
        }
 
        $trip = DB::transaction(function () use ($validated, $tripData, $request) {
            $client = Client::where('user_id', $request->user()->id)->first();
            $validated['is_ai_generated'] = true;
            $validated['estimated_expenses'] = $tripData['total_estimated_cost'] ?? 0;
 
            $tripRecord = $this->trips->create($validated);
            if ($client) {
                $tripRecord->clients()->attach($client->id);
                $this->notificationService->sendTripCreatedNotification($client);
            }
 
            foreach ($tripData['trip'] ?? [] as $day) {
                $day['hotel'] = $tripData['best_hotel'] ?? 'Not specified';
 
                $tripRecord->details()->create([
                    'day'      => $day['day'],
                    'title'    => $day['day_title'] ?? $day['weather_note'] ?? ('Day ' . $day['day']),
                    'expenses' => $day['daily_cost'] ?? 0,
                    'plan'     => json_encode($day),
                ]);
            }
 
            return $tripRecord->load('details', 'clients');
        });
 
        return response()->json($trip, 201);
    }
}
 