<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RestaurantService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiHost;

    public function __construct()
    {
        $this->baseUrl = config('services.restaurants_api.base_url');
        $this->apiKey = config('services.restaurants_api.key');
        $this->apiHost = config('services.restaurants_api.host');
    }

    private function headers()
    {
        return [
            'Content-Type' => 'application/json',
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ];
    }

    public function listRestaurants(array $filters = [])
    {
        return Cache::remember(
            'restaurants_' . md5(json_encode($filters)),
            now()->addMinutes(30),
            function () use ($filters) {

                // Step 1: Search Location
                $locationResponse = Http::withHeaders($this->headers())
                    ->get($this->baseUrl . '/searchLocation', [
                        'query' => $filters['city']
                    ]);

                if ($locationResponse->failed()) {
                    return [];
                }

                $location = $locationResponse->json();

                // عدلي السطر ده حسب شكل الـ response الحقيقي
                $locationId = data_get($location, 'data.0.locationId');

                if (!$locationId) {
                    return [];
                }

                // Step 2: Search Restaurants
                $restaurants = Http::withHeaders($this->headers())
                    ->get($this->baseUrl . '/searchRestaurants', [
                        'locationId' => $locationId,
                        'page' => $filters['page'] ?? 0
                    ]);

                if ($restaurants->failed()) {
                    return [];
                }

                return $restaurants->json();
            }
        );
    }

    public function getRestaurantDetails(string $id)
    {
        return Cache::remember(
            'restaurant_' . $id,
            now()->addHour(),
            function () use ($id) {

                $response = Http::withHeaders($this->headers())
                    ->get($this->baseUrl . '/getRestaurantDetailsV2', [
                        'restaurantsId' => $id,
                        'currencyCode' => 'USD'
                    ]);

                return $response->failed() ? null : $response->json();
            }
        );
    }
}