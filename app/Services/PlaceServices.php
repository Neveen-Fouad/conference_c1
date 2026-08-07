<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    $key = 'booking_location_' . md5($city);

    $cached = Cache::get($key);
    if ($cached !== null) {
        return $cached;
    }

    $response = Http::withHeaders($this->headers())
        ->get("{$this->baseUrl}/attraction/searchLocation", [
            'query' => $city,
            'languagecode' => 'en-us',
        ]);

    if (!$response->successful()) {
        Log::warning('searchLocation failed', ['status' => $response->status(), 'body' => $response->json()]);
        return null;
    }
    if ($response->status() === 429) {
    Log::error('RapidAPI quota exceeded for Booking.com15', ['body' => $response->body()]);
    return null; 
}

    $destinations = collect($response->json('data.destinations', []));

    $match = $destinations->first(fn ($d) =>
        strcasecmp($d['cityName'] ?? '', $city) === 0
    ) ?? $destinations->first();

    if (!$match) {
        Log::warning('No destination match found for city', ['city' => $city]);
        return null;
    }

    $id = $match['id'] ?? null;

    if ($id) {
        Cache::put($key, $id, now()->addWeek()); 
    }

    return $id;
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

    $cacheKey = 'booking_attractions_' . $locationId;
    $all = Cache::get($cacheKey);

    if ($all === null) {
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
                Log::warning('searchAttractions failed', [
                    'page' => $page,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                break;
            }

            $pageProducts = $response->json('data.products', []);

            if (empty($pageProducts)) {
                break;
            }

            $products = $products->merge($pageProducts);
        }

        $all = $products->map(function ($place) {
            return [
                'name' => $place['name'] ?? null,
                'description' => $place['shortDescription'] ?? '',
                'rating' => $place['reviewsStats']['combinedNumericStats']['average'] ?? null,
                'photo' => $place['primaryPhoto']['small'] ?? null,
            ];
        })->filter(fn ($p) => $p['name'])->unique('name')->values()->toArray();

        if (!empty($all)) {
            Cache::put($cacheKey, $all, now()->addHours(6));
        }
    }
      $interestSlug = strtolower($interestSlug);

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