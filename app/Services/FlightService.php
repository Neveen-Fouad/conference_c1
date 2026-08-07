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
        $this->baseUrl = config('services.flights_api.base_url', 'https://sky-scrapper.p.rapidapi.com/api');
        $this->apiKey = config('services.flights_api.key');
        $this->apiHost = config('services.flights_api.host', 'sky-scrapper.p.rapidapi.com');
    }

    private function http()
    {
        return Http::withHeaders([
            'x-rapidapi-key' => $this->apiKey,
            'x-rapidapi-host' => $this->apiHost,
            'Accept' => 'application/json',
        ]);
    }


    public function searchAirport(string $query)
    {
        $cleanQuery = strtolower(trim($query));

      
        $knownPlaces = [
            'cairo' => ['skyId' => 'CAIR', 'entityId' => '27539733'],
            'london' => ['skyId' => 'LOND', 'entityId' => '27544008'],
            'new york' => ['skyId' => 'NYCA', 'entityId' => '27537542'],
            'dubai' => ['skyId' => 'DXBA', 'entityId' => '27539791'],
            'madrid' => ['skyId' => 'MADA', 'entityId' => '27544833'],
            'paris' => ['skyId' => 'PARI', 'entityId' => '27539733'],
        ];

        foreach ($knownPlaces as $city => $data) {
            if (str_contains($cleanQuery, $city)) {
                return $data;
            }
        }

        $response = $this->http()->get($this->baseUrl . '/v1/flights/searchAirport', [
            'query' => trim($query),
            'locale' => 'en-US',
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json()['data'] ?? [];

        if (empty($data)) {
            return null;
        }

        $item = $data[0];

        $skyId = $item['skyId'] ?? $item['navigation']['skyId'] ?? null;
        $entityId = $item['entityId'] ?? $item['navigation']['entityId'] ?? null;

        if (!$skyId || !$entityId) {
            return null;
        }

        return [
            'skyId' => $skyId,
            'entityId' => $entityId,
        ];
    }


    public function searchFlights(array $filters)
    {
        $origin = $this->searchAirport($filters['origin_city']);
        $destination = $this->searchAirport($filters['destination_city']);

        if (!$origin || !$destination) {
            return [
                'status' => false,
                'message' => 'Airport details not found on RapidAPI.',
                'origin_city_searched' => $filters['origin_city'],
                'destination_city_searched' => $filters['destination_city'],
                'origin_found' => (bool)$origin,
                'destination_found' => (bool)$destination,
            ];
        }

        $cacheKey = 'flights_search_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($origin, $destination, $filters) {

            $queryParams = [
                'originSkyId' => $origin['skyId'],
                'destinationSkyId' => $destination['skyId'],
                'originEntityId' => $origin['entityId'],
                'destinationEntityId' => $destination['entityId'],
                'date' => $filters['departure_date'],
                'returnDate' => $filters['return_date'] ?? null,
                'cabinClass' => $filters['cabin_class'] ?? 'economy',
                'adults' => $filters['travelers'] ?? $filters['adults'] ?? 1,
                'sortBy' => $filters['sort_by'] ?? 'best',
                'currency' => $filters['currency'] ?? 'USD',
                'market' => $filters['market'] ?? 'en-US',
                'countryCode' => $filters['country_code'] ?? 'US',
            ];

            $queryParams = array_filter($queryParams, fn($value) => !is_null($value));

            $response = $this->http()->get($this->baseUrl . '/v2/flights/searchFlights', $queryParams);

            if ($response->failed()) {
                return $response->json();
            }

            return $response->json() ?? [];
        });
    }


    public function getFlightDetails(array $params)
    {
        $queryParams = [
            'itineraryId' => $params['itineraryId'],
            'sessionId' => $params['sessionId'],
            'legs' => is_array($params['legs']) ? json_encode($params['legs']) : $params['legs'],
            'adults' => $params['adults'] ?? 1,
            'currency' => $params['currency'] ?? 'USD',
            'locale' => $params['locale'] ?? 'en-US',
            'market' => $params['market'] ?? 'en-US',
            'cabinClass' => $params['cabinClass'] ?? 'economy',
            'countryCode' => $params['countryCode'] ?? 'US',
        ];

        $response = $this->http()->get($this->baseUrl . '/v1/flights/getFlightDetails', $queryParams);

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}