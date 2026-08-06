<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class FlightService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiHost;

    public function __construct()
    {
    $this->baseUrl = config('services.flights.base_url');
    $this->apiKey  = config('services.flights.key');
    $this->apiHost = config('services.flights.host');
    }

    public function searchFlights(array $params)
    {
        $cacheKey = 'flights_search_' . md5(json_encode($params));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($params) {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl . '/searchFlights', $params);

            if ($response->failed()) return [];
            return $response->json()['data']['itineraries'] ?? [];
        });
    }

    public function getFlightDetails(string $id)
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ])->get($this->baseUrl . '/flightDetails', ['id' => $id]);

        return $response->failed() ? null : $response->json();
    }
}