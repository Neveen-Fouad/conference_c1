<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Log;

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
        $this->locale = config('services.hotels.locale');
        $this->domain = config('services.hotels.domain');
    }

    public function getHotelDetails(string $hotelId)
    {
        $cacheKey = "hotel:{$hotelId}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($hotelId) {
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
                        'hotel_id' => $hotelId, 'status' => $response->status(), 'body' => $response->body(),
                    ]);
                    return null;
                }
                $hotel = $response->json('data');

                return $hotel;
            } catch (\Throwable $e) {
                Log::error('Hotels.com get hotel details threw an exception', [
                    'message' => $e->getMessage(), 'hotelId' => $hotelId,
                ]);
                return null;
            }
        });
    }

    public function getHotelOffers(string $hotelId, string $checkIn, string $checkOut, int $guests)
    {
        $cacheKey = "hotel_offers:{$hotelId}:{$checkIn}:{$checkOut}:{$guests}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($hotelId, $checkIn, $checkOut, $guests) {
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
                        'hotel_id' => $hotelId, 'status' => $response->status(), 'body' => $response->body(),
                    ]);
                    return null;
                }

                return $response->json('data');
            } catch (\Throwable $e) {
                Log::error('Hotels.com get hotel offers threw an exception', [
                    'message' => $e->getMessage(), 'hotelId' => $hotelId,
                ]);
                return null;
            }
        });
    }
}