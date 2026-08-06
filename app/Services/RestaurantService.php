<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RestaurantService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiHost;

    public function __construct()
    {
        $this->baseUrl = config('services.restaurants_api.base_url');
        $this->apiKey  = config('services.restaurants_api.key');
        $this->apiHost = config('services.restaurants_api.host');
    }

    public function listRestaurants(array $filters = [])
    {
        $cacheKey = 'restaurants_list_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filters) {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl . '/search', $filters);

            if ($response->failed()) return [];
            return $response->json()['data'] ?? [];
        });
    }

    public function getRestaurantDetails(string $id)
    {
        $cacheKey = 'restaurant_details_' . $id;

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($id) {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl . '/details', ['id' => $id]);

            return $response->failed() ? null : $response->json();
        });
    }
}