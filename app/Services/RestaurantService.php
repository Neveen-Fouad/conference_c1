<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function listRestaurants(array $filters)
    {
        $cacheKey = 'restaurants_list_'.md5(json_encode($filters));

        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($filters) {
            try {
                $response = Http::withHeaders([
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => $this->apiHost,
                ])->get($this->baseUrl.'/restaurants/list', [
                    'query' => $filters['city'] ?? '',
                    'page' => $filters['page'] ?? 1,
                    'min_rating' => $filters['min_rating'] ?? null,
                ]);

                if ($response->failed()) {
                    Log::error('Failed to fetch restaurants: '.$response->body());
                    throw new \Exception('Failed to fetch restaurants: '.$response->body());
                }

                return $response->json();
            } catch (\Exception $e) {
                throw new \Exception('Error fetching restaurants: '.$e->getMessage());
            }
        });
    }

    public function getRestaurantDetails(string $query)
    {
        $cacheKey = 'restaurant_details_'.md5($query);

        return Cache::remember($cacheKey, now()->addHours(1), function () use ($query) {
            try {
                $response = Http::withHeaders([
                    'X-RapidAPI-Key' => $this->apiKey,
                    'X-RapidAPI-Host' => $this->apiHost,
                ])->get($this->baseUrl.'/restaurants/detail', [
                    'query' => $query,
                ]);

                if ($response->failed()) {
                    Log::error('Failed to fetch restaurant details for query {query}', ['query' => $query, 'status' => $response->status(), 'body' => $response->body()]);
                    throw new \Exception('Failed to fetch restaurant details for query '.$query.': '.$response->body());
                }

                return $response->json();
            } catch (\Exception $e) {
                throw new \Exception('Error fetching restaurant details: '.$e->getMessage());
            }
        });
    }
    
       /**
     * Normalized details used for favourites (and similar card contexts).
     */
    public function favouriteDetails(string $query): ?array
    {
        try {
            $restaurant = $this->getRestaurantDetails($query);
        } catch (\Throwable) {
            return null;
        }

        if (is_array($restaurant) && array_key_exists(0, $restaurant)) {
            $restaurant = $restaurant[0];
        }

        $data = is_array($restaurant) ? ($restaurant['data'] ?? $restaurant) : [];

        $images = $data['images'] ?? [];
        if (is_array($images)) {
            $images = array_values($images);
        }

        return [
            'name' => $data['name'] ?? $restaurant['name'] ?? $restaurant['title'] ?? null,
            'description' => $data['description'] ?? $data['about'] ?? null,
            'image' => is_array($images) ? ($images[0] ?? null) : ($data['image'] ?? null),
            'location' => $data['address'] ?? $data['location'] ?? null,
            'rating' => $data['rating'] ?? $data['averageRating'] ?? $data['average_rating'] ?? null,
        ];
    }
}
