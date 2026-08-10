<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SearchService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiHost;
    protected string $locale;
    protected string $domain;

    public function __construct()
    {
        $this->baseUrl = config('services.hotels.base_url');
        $this->apiKey = config('services.hotels.key');
        $this->apiHost = config('services.hotels.host');
        $this->locale = config('services.hotels.locale');
        $this->domain = config('services.hotels.domain');
    }

    /**
     * Resolve destination to region id.
     */
    public function resolveRegion(string $query): ?string
    {
        $cacheKey = 'region:' . md5($query);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($query) {

            try {
                $response = Http::withHeaders([
                    'x-rapidapi-key' => $this->apiKey,
                    'x-rapidapi-host' => $this->apiHost,
                ])->get($this->baseUrl . '/v2/regions', [
                    'query' => $query,
                    'locale' => $this->locale,
                    'domain' => $this->domain,
                ]);

                if ($response->failed()) {
                    Log::warning('Hotels.com region resolve failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'query' => $query,
                    ]);

                    return null;
                }

                $regionId = data_get($response->json(), 'data.0.gaiaId');

                if (!$regionId) {
                    Log::warning('No region found.', [
                        'query' => $query,
                        'response' => $response->json(),
                    ]);

                    return null;
                }

                return (string) $regionId;

            } catch (\Throwable $e) {

                Log::error('Hotels.com region resolve threw an exception', [
                    'message' => $e->getMessage(),
                    'query' => $query,
                ]);

                return null;
            }
        });
    }

    /**
     * Search available hotels.
     */
    public function searchHotels(array $filters): array
{
    $regionId = $this->resolveRegion($filters['destination']);

    if (!$regionId) {
        return [
            'message' => 'Unable to resolve destination.',
            'hotels' => [],
        ];
    }

    $requestFilters = [
        'destination'       => $filters['destination'],
        'budget'            => $filters['budget'],
        'available_filter'  => 'SHOW_AVAILABLE_ONLY',
        'region_id'         => $regionId,
        'checkin_date'      => $filters['check_in'],
        'checkout_date'     => $filters['check_out'],
        'adults_number'     => $filters['guests'],
        'locale'            => $this->locale,
        'domain'            => $this->domain,
        'sort_order'        => 'REVIEW',
    ];

        $cacheKey = 'hotel_search:' . md5(json_encode($requestFilters));

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($requestFilters, $filters, $regionId) {

            try {

                $response = Http::withHeaders([
                    'x-rapidapi-key' => $this->apiKey,
                    'x-rapidapi-host' => $this->apiHost,
                ])->get(
                    $this->baseUrl . '/v3/hotels/search',
                    $requestFilters
                );

                if ($response->failed()) {

                    Log::error('Failed to search hotels', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'regionId' => $regionId,
                    ]);

                    return [];
                }
                
                $hotels = $response->json('data', []);

                return [
                    'destination' => $filters['destination'],
                    'region_id' => $regionId,
                    'check_in' => $filters['check_in'],
                    'check_out' => $filters['check_out'],
                    'guests' => $filters['guests'],
                    'budget' => $filters['budget'],
                    'count' => $response->json('data.count', 0),
                    'hotels' => $hotels,
                ];

            } catch (\Throwable $e) {

                Log::error('Hotels.com search threw an exception', [
                    'message' => $e->getMessage(),
                    'regionId' => $regionId,
                ]);

                return [];
            }
        });
    }
}