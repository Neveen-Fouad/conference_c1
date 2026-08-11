<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TransportationService
{
    public const PROFILE_WALKING = 'foot-walking';
    public const PROFILE_DRIVING = 'driving-car';

    protected string $baseUrl;
    protected ?string $apiKey;
    protected int $cacheTtlDays;

    public function __construct()
    {
        $this->baseUrl = config('services.ors.base_url', 'https://api.openrouteservice.org');
        $this->apiKey = config('services.ors.api_key');
        $this->cacheTtlDays = (int) config('services.ors.cache_ttl_days', 7);
    }

    
    public function getSmartTravelTimes(array $origin, array $destinations): array
    {
        if (empty($destinations)) {
            return [];
        }

        $walkingTimes = $this->getTravelTimes($origin, $destinations, self::PROFILE_WALKING);
        $drivingTimes = $this->getTravelTimes($origin, $destinations, self::PROFILE_DRIVING);

        $smartResults = [];

        foreach ($destinations as $dest) {
            $id = $dest['id'];
            $walk = $walkingTimes[$id] ?? null;
            $drive = $drivingTimes[$id] ?? null;

            if ($walk && !$walk['is_estimate'] && $walk['duration_minutes'] <= 15) {
                $smartResults[$id] = $walk;
            } elseif ($drive) {
                $smartResults[$id] = $drive;
            } elseif ($walk) {
                $smartResults[$id] = $walk;
            } else {
                $smartResults[$id] = ['label' => 'Unknown', 'duration_minutes' => 0, 'distance_meters' => null, 'is_estimate' => true];
            }
        }

        return $smartResults;
    }

    public function getTravelTimes(array $origin, array $destinations, string $profile = self::PROFILE_WALKING): array
    {
        if (empty($destinations)) {
            return [];
        }

        $cacheKey = $this->buildCacheKey($origin, $destinations, $profile);

        return Cache::remember($cacheKey, now()->addDays($this->cacheTtlDays), function () use ($origin, $destinations, $profile) {
            return $this->fetchMatrix($origin, $destinations, $profile);
        });
    }

    protected function fetchMatrix(array $origin, array $destinations, string $profile): array
    {
        if (empty($this->apiKey)) {
            Log::warning('TransportationService: ORS_API_KEY not configured, using haversine estimates');
            return $this->fallbackAll($origin, $destinations, $profile);
        }

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
                    'profile' => $profile,
                ]);
                return $this->fallbackAll($origin, $destinations, $profile);
            }

            return $this->mapMatrixResponse($response->json(), $origin, $destinations, $profile);
        } catch (\Throwable $e) {
            Log::warning('ORS matrix exception, falling back to haversine estimate', [
                'message' => $e->getMessage(),
                'profile' => $profile,
            ]);
            return $this->fallbackAll($origin, $destinations, $profile);
        }
    }

    protected function mapMatrixResponse(?array $data, array $origin, array $destinations, string $profile): array
    {
        $durations = data_get($data, 'durations.0');
        $distances = data_get($data, 'distances.0');

        if (!is_array($durations)) {
            return $this->fallbackAll($origin, $destinations, $profile);
        }

        $results = [];

        foreach (array_values($destinations) as $i => $dest) {
            $durationSeconds = $durations[$i] ?? null;

            if ($durationSeconds === null) {
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

    protected function fallbackAll(array $origin, array $destinations, string $profile): array
    {
        $results = [];
        foreach ($destinations as $dest) {
            $results[$dest['id']] = $this->fallbackSingle($origin, $dest, $profile);
        }
        return $results;
    }

    protected function fallbackSingle(array $origin, array $destination, string $profile): array
    {
        $distanceMeters = $this->haversineMeters(
            $origin['lat'], $origin['lng'],
            $destination['lat'], $destination['lng']
        );

        $speedMetersPerMinute = $profile === self::PROFILE_DRIVING ? 500.0 : 80.0;
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