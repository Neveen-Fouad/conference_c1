<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Wraps the OpenRouteService (ORS) Matrix API to produce walk/drive-time tips
 * between one origin (hotel or airport) and many destinations (restaurants,
 * attractions, hotels) in a single batched request.
 *
 * Docs: https://openrouteservice.org/dev/#/api-docs/v2/matrix/{profile}/post
 * Matrix limit: 3,500 origin x destination routes per request (e.g. 50 x 50).
 * We only ever send 1 origin, so up to 3,500 destinations fit in one call;
 * MAX_DESTINATIONS_PER_REQUEST below chunks defensively well under that.
 */
class TransportationService
{
    public const PROFILE_WALKING = 'foot-walking';
    public const PROFILE_DRIVING = 'driving-car';

    protected const MAX_DESTINATIONS_PER_REQUEST = 50;

    protected string $baseUrl;
    protected ?string $apiKey;
    protected int $cacheTtlDays;

    public function __construct()
    {
        $this->baseUrl = config('services.ors.base_url', 'https://api.openrouteservice.org');
        $this->apiKey = config('services.ors.api_key');
        $this->cacheTtlDays = (int) config('services.ors.cache_ttl_days', 7);
    }

    /**
     * Get travel-time tips from one origin to many destinations.
     *
     * @param array{lat: float, lng: float} $origin
     * @param array<int, array{id: string|int, lat: float, lng: float}> $destinations
     * @param string $profile self::PROFILE_WALKING | self::PROFILE_DRIVING
     * @return array<string, array> keyed by destination id, see mapMatrixResponse() for shape
     */
    public function getTravelTimes(array $origin, array $destinations, string $profile = self::PROFILE_WALKING): array
    {
        if (empty($destinations)) {
            return [];
        }

        if (!in_array($profile, [self::PROFILE_WALKING, self::PROFILE_DRIVING], true)) {
            Log::warning('TransportationService: unsupported profile requested, defaulting to walking', [
                'profile' => $profile,
            ]);
            $profile = self::PROFILE_WALKING;
        }

        $results = [];

        foreach (array_chunk($destinations, self::MAX_DESTINATIONS_PER_REQUEST) as $chunk) {
            $cacheKey = $this->buildCacheKey($origin, $chunk, $profile);

            $chunkResults = Cache::remember($cacheKey, now()->addDays($this->cacheTtlDays), function () use ($origin, $chunk, $profile) {
                return $this->fetchMatrix($origin, $chunk, $profile);
            });

            $results += $chunkResults;
        }

        return $results;
    }

    /** Convenience wrapper: hotel <-> nearby restaurants/attractions. */
    public function getWalkingTips(array $origin, array $destinations): array
    {
        return $this->getTravelTimes($origin, $destinations, self::PROFILE_WALKING);
    }

    /** Convenience wrapper: airport <-> hotel. */
    public function getDrivingTips(array $origin, array $destinations): array
    {
        return $this->getTravelTimes($origin, $destinations, self::PROFILE_DRIVING);
    }

    /**
     * Calls ORS's matrix endpoint once for the whole batch:
     * locations = [origin, dest0, dest1, ...], sources = [0], destinations = [1..n].
     */
    protected function fetchMatrix(array $origin, array $destinations, string $profile): array
    {
        if (empty($this->apiKey)) {
            Log::warning('TransportationService: ORS_API_KEY not configured, using haversine estimates only');
            return $this->fallbackAll($origin, $destinations, $profile);
        }

        // ORS expects [lng, lat] order.
        $locations = [[$origin['lng'], $origin['lat']]];
        foreach ($destinations as $dest) {
            $locations[] = [$dest['lng'], $dest['lat']];
        }

        try {
            $response = Http::withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(8)
                ->post("{$this->baseUrl}/v2/matrix/{$profile}", [
                    'locations' => $locations,
                    'sources' => [0],
                    'destinations' => range(1, count($destinations)),
                    'metrics' => ['duration', 'distance'],
                    'units' => 'm',
                ]);

            if ($response->failed()) {
                Log::warning('ORS matrix request failed, falling back to haversine estimate', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'profile' => $profile,
                ]);
                return $this->fallbackAll($origin, $destinations, $profile);
            }

            return $this->mapMatrixResponse($response->json(), $origin, $destinations, $profile);
        } catch (\Throwable $e) {
            // Covers timeouts and connection errors so the page never breaks.
            Log::warning('ORS matrix request threw an exception, falling back to haversine estimate', [
                'message' => $e->getMessage(),
                'profile' => $profile,
            ]);
            return $this->fallbackAll($origin, $destinations, $profile);
        }
    }

    /**
     * Maps ORS's response onto our destination ids.
     * Response shape (per ORS matrix docs):
     *   durations[0][i] -> seconds from source 0 to destination i
     *   distances[0][i] -> meters from source 0 to destination i
     */
    protected function mapMatrixResponse(?array $data, array $origin, array $destinations, string $profile): array
    {
        $durations = data_get($data, 'durations.0');
        $distances = data_get($data, 'distances.0');

        if (!is_array($durations)) {
            Log::warning('ORS matrix response missing durations.0, falling back to haversine estimate', [
                'profile' => $profile,
            ]);
            return $this->fallbackAll($origin, $destinations, $profile);
        }

        $results = [];

        foreach (array_values($destinations) as $i => $dest) {
            $durationSeconds = $durations[$i] ?? null;

            if ($durationSeconds === null) {
                // ORS returns null for a specific pair (e.g. unreachable on foot) even
                // when the request itself succeeds — estimate just that one row.
                $results[$dest['id']] = $this->fallbackSingle($origin, $dest, $profile);
                continue;
            }

            $distanceMeters = $distances[$i] ?? null;
            $durationMinutes = (int) round($durationSeconds / 60);

            $results[$dest['id']] = [
                'destination_id' => $dest['id'],
                'profile' => $profile,
                'duration_minutes' => $durationMinutes,
                'distance_meters' => $distanceMeters !== null ? (int) round($distanceMeters) : null,
                'label' => $this->formatLabel($durationMinutes, $profile),
                'is_estimate' => false,
            ];
        }

        return $results;
    }

    /** Fallback for the whole batch when ORS can't be reached at all. */
    protected function fallbackAll(array $origin, array $destinations, string $profile): array
    {
        $results = [];
        foreach ($destinations as $dest) {
            $results[$dest['id']] = $this->fallbackSingle($origin, $dest, $profile);
        }
        return $results;
    }

    /**
     * Haversine-based estimate for a single origin/destination pair, used when ORS
     * is down, times out, or a single row is missing from an otherwise-successful response.
     */
    protected function fallbackSingle(array $origin, array $destination, string $profile): array
    {
        $distanceMeters = $this->haversineMeters(
            $origin['lat'], $origin['lng'],
            $destination['lat'], $destination['lng']
        );

        // Rough average speeds, for estimation only — not meant to match real routing.
        $speedMetersPerMinute = $profile === self::PROFILE_DRIVING
            ? 500.0  // ~30 km/h average city driving
            : 80.0;  // ~4.8 km/h average walking pace

        $durationMinutes = max(1, (int) round($distanceMeters / $speedMetersPerMinute));

        return [
            'destination_id' => $destination['id'],
            'profile' => $profile,
            'duration_minutes' => $durationMinutes,
            'distance_meters' => (int) round($distanceMeters),
            'label' => $this->formatLabel($durationMinutes, $profile, isEstimate: true),
            'is_estimate' => true,
        ];
    }

    /** Great-circle distance between two lat/lng points, in meters. */
    protected function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusMeters = 6371000;

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusMeters * $c;
    }

    /**
     * Matches the "12 min walk" style string the hotel API already returns for
     * nearbyPOIs, so the frontend can render both sources with the same component.
     */
    protected function formatLabel(int $minutes, string $profile, bool $isEstimate = false): string
    {
        $mode = $profile === self::PROFILE_DRIVING ? 'drive' : 'walk';
        $label = "{$minutes} min {$mode}";

        return $isEstimate ? "{$label} (est.)" : $label;
    }

    protected function buildCacheKey(array $origin, array $destinations, string $profile): string
    {
        $originPart = round($origin['lat'], 5) . ',' . round($origin['lng'], 5);
        $destIds = collect($destinations)->pluck('id')->sort()->implode(',');

        return 'ors_matrix_' . md5("{$profile}:{$originPart}:{$destIds}");
    }
}