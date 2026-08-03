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
                Log::info($response->status(), $response->json());
                return null;
            }
            return $response->json('data.0.id')
                ?? $response->json('data.products.0.id');
        });
    }

    // Maps an interest slug -> keywords to match against attraction name/category
    protected array $interestKeywords = [
        'hiking'     => ['hiking', 'trail', 'trek', 'nature walk'],
        'beaches'    => ['beach', 'coast', 'seaside'],
        'camping'    => ['camp', 'outdoor', 'wilderness'],
        'museums'    => ['museum', 'gallery', 'exhibit'],
        'historical' => ['historic', 'heritage', 'ancient', 'monument', 'castle', 'palace'],
        'shopping'   => ['shopping', 'market', 'mall', 'bazaar'],
        'adventure'  => ['adventure', 'thrill', 'extreme', 'zipline', 'diving'],
    ];

    public function getAttractions(string $city, ?string $interestSlug = null): array
    {
        $locationId = $this->getLocationId($city);
        if (!$locationId) {
            return ['results' => []];
        }

        $all = Cache::remember('booking_attractions_' . $locationId, now()->addHours(6), function () use ($city, $locationId) {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/attraction/searchAttractions", [
                    'id' => $locationId,
                    'sortBy' => 'trending',
                    'query' => $city,
                    'languagecode' => 'en-us',
                ]);

            if (!$response->successful()) {
                Log::info($response->status(), $response->json());
                return [];
            }

            return collect($response->json('data.products'))->map(function ($place) {
                return [
                    'name' => $place['name'] ?? null,
                    'rating' => $place['reviewsStats']['combinedNumericStats']['average'] ?? null,
                    'category' => $place['primaryCategory']['name'] ?? null,
                    'photo' => $place['primaryPhoto']['small'] ?? null,
                ];
            })->filter(fn ($p) => $p['name'])->values()->toArray();
        });

        if (!$interestSlug || !isset($this->interestKeywords[$interestSlug])) {
            return ['results' => $all];
        }

        $keywords = $this->interestKeywords[$interestSlug];

        $filtered = array_values(array_filter($all, function ($place) use ($keywords) {
            $haystack = strtolower(($place['name'] ?? '') . ' ' . ($place['category'] ?? ''));
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