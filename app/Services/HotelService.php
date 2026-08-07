<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HotelService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiHost;

    public function __construct()
    {
        $this->baseUrl = config('services.hotels_api.base_url');
        $this->apiKey  = config('services.hotels_api.key');
        $this->apiHost = config('services.hotels_api.host');
    }

    public function listHotels(array $filters = [])
    {
        $cacheKey = 'hotels_list_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filters) {

            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl . '/v3/hotels/search', $filters);

            if ($response->failed()) {
                return [];
            }

            return $response->json();
        });
    }

    public function getHotelDetails(string $hotelId)
{
    $cacheKey = 'hotel_details_' . $hotelId;

    return Cache::remember($cacheKey, now()->addHour(), function () use ($hotelId) {

        $response = Http::withHeaders([
            'X-RapidAPI-Key'  => $this->apiKey,
            'X-RapidAPI-Host' => $this->apiHost,
        ])->get($this->baseUrl . '/v3/hotels/info', [
            'hotel_id' => $hotelId,
            'domain'   => 'AR',
            'locale'   => 'es_AR',
        ]);

        return $response->failed() ? null : $response->json();
    });

    }
}