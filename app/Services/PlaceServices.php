<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PlaceServices
{
    protected string $key;
    protected string $host;
    protected string $baseUrl = 'https://booking-com15.p.rapidapi.com/api/v1';

    public function __construct()
    {
        $this->key = config('services.rapidapi.key');
        $this->host = config('services.rapidapi.host');
    }

    protected function headers(): array
    {
        return [
            'x-rapidapi-key' => $this->key,
            'x-rapidapi-host' => $this->host,
        ];
    }
  protected function getLocationId(string $city): ?string
{
    return Cache::remember('booking_location_' . md5($city), now()->addWeek(), function () use ($city) {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/attraction/searchLocation", [
                'query' => $city,
                'languagecode' => 'en-us',
            ]);

        if (!$response->successful()) {
            Log::info('searchLocation failed', ['status' => $response->status(), 'body' => $response->json()]);
            return null;
        }

        $destinations = collect($response->json('data.destinations', []));

        $match = $destinations->first(fn ($d) =>
            strcasecmp($d['cityName'] ?? '', $city) === 0
        ) ?? $destinations->first();

        if (!$match) {
            Log::info('No destination match found for city', ['city' => $city]);
        }

        return $match['id'] ?? null;   
    });
}
    protected array $interestKeywords = [
    'adventure'   => ['adventure', 'thrill', 'extreme', 'zipline', 'diving'],
    'beach'       => ['beach', 'coast', 'seaside'],
    'nature'      => ['nature', 'park', 'garden', 'wildlife', 'trail', 'hiking'],
    'history'     => ['historic', 'heritage', 'ancient', 'monument', 'landmark'],
    'culture'     => ['museum', 'gallery', 'art', 'culture', 'exhibit', 'theatre', 'theater'],
    'food'        => ['food', 'restaurant', 'culinary', 'dining', 'cuisine', 'wine', 'tasting'],
    'shopping'    => ['shopping', 'market', 'mall', 'bazaar'],
    'nightlife'   => ['nightlife', 'bar', 'club', 'party', 'night tour'],
    'luxury'      => ['luxury', 'vip', 'premium', 'exclusive', 'private tour'],
    'family'      => ['family', 'kids', 'children'],
    'photography' => ['photo', 'photography', 'scenic', 'viewpoint'],
    'relaxation'  => ['spa', 'relax', 'wellness', 'massage', 'yoga'],
];
public function getAttractions(string $city, ?string $interestSlug = null): array
{
    $locationId = $this->getLocationId($city);
    if (!$locationId) {
        return ['results' => []];
    }

    $all = Cache::remember('booking_attractions_' . $locationId, now()->addHours(6), function () use ($city, $locationId) {
        $products = collect();

        
        for ($page = 1; $page <= 5; $page++) {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/attraction/searchAttractions", [
                    'id' => $locationId,
                    'sortBy' => 'trending',
                    'query' => $city,
                    'languagecode' => 'en-us',
                    'page' => $page,
                ]);

            if (!$response->successful()) {
                Log::info('searchAttractions failed', ['page' => $page, 'status' => $response->status()]);
                break;
            }

            $pageProducts = $response->json('data.products', []);

            if (empty($pageProducts)) {
                break; 
            }

            $products = $products->merge($pageProducts);
        }

        return $products->map(function ($place) {
            return [
                'name' => $place['name'] ?? null,
                'description' => $place['shortDescription'] ?? '',
                'rating' => $place['reviewsStats']['combinedNumericStats']['average'] ?? null,
                'photo' => $place['primaryPhoto']['small'] ?? null,
            ];
        })->filter(fn ($p) => $p['name'])->unique('name')->values()->toArray();
    });

    if (!$interestSlug || !isset($this->interestKeywords[$interestSlug])) {
        return ['results' => $all];
    }

    $keywords = $this->interestKeywords[$interestSlug];

    $filtered = array_values(array_filter($all, function ($place) use ($keywords) {
        $haystack = strtolower(($place['name'] ?? '') . ' ' . ($place['description'] ?? ''));
        foreach ($keywords as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }
        return false;
    }));

    return ['results' => $filtered];
}

    public function getRestaurants(string $city): array
    {
        return ['results' => []];
    }
}