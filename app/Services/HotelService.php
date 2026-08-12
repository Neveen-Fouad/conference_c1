<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
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
        $this->apiKey  = config('services.hotels.key');
        $this->apiHost = config('services.hotels.host');
        $this->locale  = config('services.hotels.locale');
        $this->domain  = config('services.hotels.domain');
    }

    /**
     * Get hotels from API.
     * If API fails or returns no hotels, use local hotel.json fallback.
     */
    public function getHotels(array $params = [])
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-RapidAPI-Key'  => $this->apiKey,
                    'X-RapidAPI-Host' => $this->apiHost,
                ])
                ->get($this->baseUrl . '/v3/hotels/search', array_merge([
                    'page_number'   => 1,
                    'checkin_date'  => now()->addDays(1)->format('Y-m-d'),
                    'checkout_date' => now()->addDays(2)->format('Y-m-d'),
                    'region_id'     => 767,
                    'adults_number' => 1,
                    'locale'        => $this->locale,
                    'domain'         => $this->domain,
                    'sort_order'    => 'REVIEW',
                ], $params));

            if ($response->successful()) {
                $data = $response->json();

                if (!empty($data['data']['properties'])) {
                    $hotels = $data['data']['properties'];

                    Cache::put(
                        'hotels_search:' . md5(json_encode($params)),
                        $hotels,
                        now()->addMinutes(10)
                    );

                    return $hotels;
                }

                Log::warning(
                    'Hotels API returned no properties. Using local fallback.'
                );
            } else {
                Log::error('Hotels API request failed.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Hotels API exception. Using local fallback.', [
                'message' => $e->getMessage(),
            ]);
        }

        return $this->getHotelsFromJson();
    }

    /**
     * Get hotels from local JSON fallback.
     */
    private function getHotelsFromJson(): array
    {
        $path = database_path('Data/External-APIs/hotel.json');

        if (!file_exists($path)) {
            Log::error('Hotel fallback JSON file not found.', [
                'path' => $path,
            ]);

            return [];
        }

        try {
            $json = file_get_contents($path);

            $hotels = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid hotel.json.', [
                    'error' => json_last_error_msg(),
                ]);

                return [];
            }

            return is_array($hotels) ? $hotels : [];
        } catch (\Throwable $e) {
            Log::error('Failed to read hotel fallback JSON.', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get hotel details.
     */
    public function getHotelDetails(string $hotelId)
    {
        $cacheKey = "hotel:{$hotelId}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key'  => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl . '/v2/hotels/details', [
                'hotel_id' => $hotelId,
                'domain'   => $this->domain,
                'locale'   => $this->locale,
            ]);

            if ($response->failed()) {
                Log::error('Failed to retrieve hotel details.', [
                    'hotel_id' => $hotelId,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);

                return null;
            }

            $hotel = $response->json();

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

            return null;
        }
    }

    /**
     * Get hotel offers.
     */
    public function getHotelOffers(
        string $hotelId,
        string $checkIn,
        string $checkOut,
        int $guests
    ) {
        $cacheKey =
            "hotel_offers:{$hotelId}:{$checkIn}:{$checkOut}:{$guests}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        Log::debug('Offers request params', [
            'hotel_id'      => $hotelId,
            'checkin_date'  => $checkIn,
            'checkout_date' => $checkOut,
            'adults_number' => $guests,
            'domain'        => $this->domain,
            'locale'        => $this->locale,
        ]);

        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key'  => $this->apiKey,
                'X-RapidAPI-Host' => $this->apiHost,
            ])->get($this->baseUrl . '/v3/hotels/offers', [
                'hotel_id'      => $hotelId,
                'checkin_date'  => $checkIn,
                'checkout_date' => $checkOut,
                'adults_number' => $guests,
                'domain'        => $this->domain,
                'locale'        => $this->locale,
            ]);

            if ($response->failed()) {
                Log::error('Failed to retrieve hotel offers.', [
                    'hotel_id' => $hotelId,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
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
    
    }
