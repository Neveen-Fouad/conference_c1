<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransportationService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        // Add a TripGo API key to your .env file: TRIPGO_API_KEY=your_key_here
        $this->apiKey = env('TRIPGO_API_KEY', 'your_backup_key_here');
        $this->baseUrl = 'https://api.tripgo.com/v1';
    }

    /**
     * Get real transit routing between two GPS coordinates
     */
    public function getRoutes($fromLat, $fromLng, $toLat, $toLng): array
    {
        if (!$fromLat || !$fromLng || !$toLat || !$toLng) {
            return ['error' => 'Missing coordinates'];
        }

        try {
            $response = Http::withHeaders([
                'X-TripGo-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/routing.json", [
                'from' => "({$fromLat},{$fromLng})",
                'to' => "({$toLat},{$toLng})",
                'v' => 11 
            ]);

            if ($response->failed()) {
                return ['error' => 'Routing API failed'];
            }

            // Grab the best route option
            $data = $response->json();
            $bestRoute = data_get($data, 'routing.groups.0.trips.0', []);
            
            $depart = data_get($bestRoute, 'depart');
            $arrive = data_get($bestRoute, 'arrive');
            
            return [
                'duration_minutes' => ($arrive && $depart) ? round(($arrive - $depart) / 60) : 0,
                'cost' => data_get($bestRoute, 'moneyCost', 0),
                'currency' => data_get($bestRoute, 'currencySymbol', 'USD'),
                'transit_mode' => data_get($bestRoute, 'mainSegmentHashCode', 'transit'),
            ];

        } catch (\Throwable $e) {
            Log::error('Routing Error: ' . $e->getMessage());
            return ['error' => 'Routing calculation failed'];
        }
    }
}