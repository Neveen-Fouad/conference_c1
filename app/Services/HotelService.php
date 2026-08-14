<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HotelService
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

    public function getHotelDetails(string $hotelId)
    {
        $cacheKey = "hotel:{$hotelId}";

        // Keep the original caching behavior
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl.'/v2/hotels/details', [
                'hotel_id' => $hotelId,
                'domain' => $this->domain,
                'locale' => $this->locale,
            ]);

            if ($response->failed()) {
                Log::error('Failed to retrieve hotel details.', [
                    'hotel_id' => $hotelId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->getHotelFromJson($hotelId);
            }

            $hotel = $response->json();

            // Keep the original API response caching
            Cache::put(
                $cacheKey,
                $hotel,
                now()->addHour()
            );

            return $hotel;

        } catch (\Throwable $e) {
            Log::error(
                'Hotels.com get hotel details threw an exception',
                [
                    'message' => $e->getMessage(),
                    'hotelId' => $hotelId,
                ]
            );

            return $this->getHotelFromJson($hotelId);
        }
    }

    /**
     * Get hotel details from local JSON backup.
     */
    private function getHotelFromJson(string $hotelId): ?array
    {
        $path = database_path('Data/External-APIs/hotel.json');

        if (! file_exists($path)) {
            Log::error('Hotel fallback JSON file not found.', [
                'path' => $path,
            ]);

            return null;
        }

        try {
            $json = file_get_contents($path);

            $hotels = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid hotel.json.', [
                    'error' => json_last_error_msg(),
                ]);

                return null;
            }

            if (! is_array($hotels)) {
                return null;
            }

            foreach ($hotels as $hotel) {
                if ((string) ($hotel['id'] ?? '') === (string) $hotelId) {

                    // Cache the fallback result as well
                    Cache::put(
                        "hotel:{$hotelId}",
                        $hotel,
                        now()->addHour()
                    );

                    return $hotel;
                }
            }

            Log::warning('Hotel not found in local JSON backup.', [
                'hotel_id' => $hotelId,
            ]);

            return null;

        } catch (\Throwable $e) {
            Log::error(
                'Failed to retrieve hotel from local JSON backup.',
                [
                    'message' => $e->getMessage(),
                    'hotelId' => $hotelId,
                ]
            );

            return null;
        }
    }

    public function getHotelOffers(
        string $hotelId,
        string $checkIn,
        string $checkOut,
        int $guests
    ) {
        $cacheKey = "hotel_offers:{$hotelId}:{$checkIn}:{$checkOut}:{$guests}";

        // Keep the original caching behavior
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        Log::debug('Offers request params', [
            'hotel_id' => $hotelId,
            'checkin_date' => $checkIn,
            'checkout_date' => $checkOut,
            'adults_number' => $guests,
            'domain' => $this->domain,
            'locale' => $this->locale,
        ]);

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl.'/v3/hotels/offers', [
                'hotel_id' => $hotelId,
                'checkin_date' => $checkIn,
                'checkout_date' => $checkOut,
                'adults_number' => $guests,
                'domain' => $this->domain,
                'locale' => $this->locale,
            ]);

            if ($response->failed()) {
                Log::error('Failed to retrieve hotel offers.', [
                    'hotel_id' => $hotelId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $offers = $response->json('data');

            Cache::put(
                $cacheKey,
                $offers,
                now()->addMinutes(15)
            );

            return $offers;

        } catch (\Throwable $e) {
            Log::error(
                'Hotels.com get hotel offers threw an exception',
                [
                    'message' => $e->getMessage(),
                    'hotelId' => $hotelId,
                ]
            );

            return null;
        }
    }
}
