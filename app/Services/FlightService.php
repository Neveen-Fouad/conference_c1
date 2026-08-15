<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlightService
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $apiHost;

    public function __construct()
    {
        $this->baseUrl = config('services.flights_api.base_url');
        $this->apiKey = config('services.flights_api.key');
        $this->apiHost = config('services.flights_api.host');
    }

    protected function headers(): array
    {
        return [
            'X-RapidAPI-Key' => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ];
    }

    public function searchAirport(string $query): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/api/v1/flights/searchAirport", [
                'query' => $query,
            ]);

        $response->throw();

        return collect($response->json('data', []))->map(function ($item) {
            return [
                'skyId' => $item['navigation']['relevantFlightParams']['skyId'],
                'entityId' => $item['navigation']['relevantFlightParams']['entityId'],
                'type' => $item['navigation']['entityType'],
                'title' => $item['presentation']['title'],
                'subtitle' => $item['presentation']['subtitle'] ?? null,
            ];
        })->toArray();
    }

    public function searchFlights(array $filters, int $maxPolls = 5): array
    {
        $params = array_merge([
            'currency' => 'USD',
            'market' => 'en-US',
            'countryCode' => 'US',
            'cabinClass' => 'economy',
            'adults' => 1,
            'sortBy' => 'best',
        ], $filters);

        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/api/v2/flights/searchFlights", $params);

        $response->throw();
        $data = $response->json('data');

        $itineraries = $data['itineraries'] ?? [];
        $sessionId = $data['context']['sessionId'] ?? null;
        $status = $data['context']['status'] ?? 'complete';

        $polls = 0;
        while ($status === 'incomplete' && $sessionId && $polls < $maxPolls) {
            $poll = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/api/v2/flights/searchIncomplete", [
                    'sessionId' => $sessionId,
                    'currency' => $params['currency'],
                    'market' => $params['market'],
                    'countryCode' => $params['countryCode'],
                ]);

            $poll->throw();
            $pollData = $poll->json('data');

            $itineraries = $this->mergeItineraries($itineraries, $pollData['itineraries'] ?? []);
            $sessionId = $pollData['context']['sessionId'] ?? $sessionId;
            $status = $pollData['context']['status'] ?? 'complete';
            $polls++;
        }

        if ($status === 'incomplete') {
            Log::warning('Flight search still incomplete after max polls', [
                'sessionId' => $sessionId,
                'filters' => $filters,
            ]);
        }

        $requestedDate = $filters['date'] ?? null;
        if ($requestedDate) {
            $itineraries = array_values(array_filter($itineraries, function ($it) use ($requestedDate) {
                $departure = $it['legs'][0]['departure'] ?? null;

                return $departure && str_starts_with($departure, $requestedDate);
            }));
        }

        return [
            'itineraries' => collect($itineraries)->map(fn ($it) => $this->mapItinerary($it))->toArray(),
            'sessionId' => $sessionId,
        ];
    }

    public function getFlightDetails(string $compositeId): ?array
    {
        $parts = explode('|', $compositeId);

        if (count($parts) !== 6) {
            return null;
        }

        [$itineraryId, $originSkyId, $destinationSkyId, $originEntityId, $destinationEntityId, $date] = $parts;

        return $this->findItinerary([
            'originSkyId' => $originSkyId,
            'destinationSkyId' => $destinationSkyId,
            'originEntityId' => $originEntityId,
            'destinationEntityId' => $destinationEntityId,
            'date' => $date,
        ], $itineraryId);
    }

    /**
     * Normalized details used for favourites (and similar card contexts).
     */
    public function favouriteDetails(string $compositeId): ?array
    {
        $flight = $this->getFlightDetails($compositeId);

        if (! $flight) {
            return null;
        }

        $leg = $flight['legs'][0] ?? $flight;
        $carrier = $leg['carriers'][0]['name'] ?? null;
        $origin = $flight['origin']['name'] ?? $leg['origin']['name'] ?? '';
        $destination = $flight['destination']['name'] ?? $leg['destination']['name'] ?? '';
        $route = trim($origin.' → '.$destination, ' →');

        return [
            'name' => $carrier ? $carrier.' flight' : 'Flight',
            'description' => $route ?: null,
            'image' => null,
            'location' => $route ?: null,
            'rating' => null,
            'price' => $flight['price']['amount'] ?? null,
        ];
    }

    /**
     * Find a single itinerary by id from a fresh search — used as a
     * "details" fallback since getFlightDetails isn't usable.
     */
    public function findItinerary(array $filters, string $itineraryId): ?array
    {
        $result = $this->searchFlights($filters);

        return collect($result['itineraries'])->firstWhere('id', $itineraryId);
    }

    protected function mergeItineraries(array $existing, array $incoming): array
    {
        $byId = collect($existing)->keyBy('id');
        foreach ($incoming as $item) {
            $byId->put($item['id'], $item);
        }

        return $byId->values()->toArray();
    }

    /**
     * Flatten a raw itinerary from the API into the useful fields:
     * price, overall origin/destination/times, per-leg detail
     * (with segments and carriers), fare policy, and tags.
     */
    protected function mapItinerary(array $it): array
    {
        $legs = collect($it['legs'] ?? [])->map(fn ($leg) => $this->mapLeg($leg))->toArray();

        $firstLeg = $legs[0] ?? null;
        $lastLeg = $legs[count($legs) - 1] ?? null;

        return [
            'id' => $it['id'] ?? null,
            'token' => $it['token'] ?? null,

            'price' => [
                'amount' => $it['price']['raw'] ?? null,
                'formatted' => $it['price']['formatted'] ?? null,
            ],

            // Overall trip summary (origin of first leg -> destination of last leg)
            'origin' => $firstLeg['origin'] ?? null,
            'destination' => $lastLeg['destination'] ?? null,
            'departure' => $firstLeg['departure'] ?? null,
            'arrival' => $lastLeg['arrival'] ?? null,

            'durationInMinutes' => $it['legs'][0]['durationInMinutes'] ?? null,
            'stopCount' => $it['legs'][0]['stopCount'] ?? null,
            'isSelfTransfer' => $it['isSelfTransfer'] ?? false,

            'legs' => $legs,

            'tags' => $it['tags'] ?? [],
            'score' => $it['score'] ?? null,
        ];
    }

    protected function mapLeg(array $leg): array
    {
        return [
            'id' => $leg['id'] ?? null,

            'origin' => [
                'id' => $leg['origin']['id'] ?? null,
                'name' => $leg['origin']['name'] ?? null,
                'city' => $leg['origin']['city'] ?? null,
                'country' => $leg['origin']['country'] ?? null,
            ],
            'destination' => [
                'id' => $leg['destination']['id'] ?? null,
                'name' => $leg['destination']['name'] ?? null,
                'city' => $leg['destination']['city'] ?? null,
                'country' => $leg['destination']['country'] ?? null,
            ],

            'departure' => $leg['departure'] ?? null,
            'arrival' => $leg['arrival'] ?? null,
            'durationInMinutes' => $leg['durationInMinutes'] ?? null,
            'stopCount' => $leg['stopCount'] ?? null,

            'carriers' => collect($leg['carriers']['marketing'] ?? [])->map(fn ($c) => [
                'name' => $c['name'] ?? null,
                'code' => $c['alternateId'] ?? null,
                'logoUrl' => $c['logoUrl'] ?? null,
            ])->toArray(),

            'segments' => collect($leg['segments'] ?? [])->map(fn ($seg) => [
                'origin' => $seg['origin']['name'] ?? null,
                'originCode' => $seg['origin']['displayCode'] ?? null,
                'destination' => $seg['destination']['name'] ?? null,
                'destinationCode' => $seg['destination']['displayCode'] ?? null,
                'departure' => $seg['departure'] ?? null,
                'arrival' => $seg['arrival'] ?? null,
                'flightNumber' => $seg['flightNumber'] ?? null,
                'carrier' => $seg['marketingCarrier']['name'] ?? null,
                'carrierCode' => $seg['marketingCarrier']['displayCode'] ?? null,
            ])->toArray(),
        ];
    }
}
