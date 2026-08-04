<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchHotelRequest;
use App\Http\Requests\StoreHotelBookingRequest;
use App\Models\bookings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HotelBookingsController extends Controller
{
    
    public function search(SearchHotelRequest $request)
    {
        $validated = $request->validated();

        $locationQuery = $validated['city'] ?? $validated['country_name'];

        $regionId = $this->resolveRegionId($locationQuery);

        if (! $regionId) {
            return response()->json(['message' => 'Could not resolve that destination.'], 422);
        }

        $cacheKey = 'hotel_search_' . md5(json_encode([$regionId, $validated]));

        $results = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($regionId, $validated) {
            $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
            $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);

            try {
                $response = Http::withHeaders([
                    'x-rapidapi-key' => config('services.hotels_provider.key'),
                    'x-rapidapi-host' => config('services.hotels_provider.host'),
                    'Content-Type' => 'application/json',
                ])->post('https://' . config('services.hotels_provider.host') . '/properties/v2/list', [
                    'currency' => 'USD',
                    'eapid' => 1,
                    'locale' => 'en_US',
                    'siteId' => 300000001,
                    'destination' => ['regionId' => $regionId],
                    'checkInDate' => [
                        'day' => (int) $checkIn->format('d'),
                        'month' => (int) $checkIn->format('m'),
                        'year' => (int) $checkIn->format('Y'),
                    ],
                    'checkOutDate' => [
                        'day' => (int) $checkOut->format('d'),
                        'month' => (int) $checkOut->format('m'),
                        'year' => (int) $checkOut->format('Y'),
                    ],
                    'rooms' => [
                        ['adults' => $validated['guests']],
                    ],
                    'resultsStartingIndex' => 0,
                    'resultsSize' => 50,
                    'sort' => 'PRICE_LOW_TO_HIGH',
                ]);

                if ($response->failed()) {
                    Log::warning('Hotels4 search failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'regionId' => $regionId,
                    ]);
                    return [];
                }

                return $response->json();
            } catch (\Throwable $e) {
                Log::error('Hotels4 search threw an exception', [
                    'message' => $e->getMessage(),
                    'regionId' => $regionId,
                ]);
                return [];
            }
        });

        return response()->json($results);
    }

    protected function resolveRegionId(string $query): ?string
    {
        $cacheKey = 'hotel_region_' . md5($query);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($query) {
            try {
                $response = Http::withHeaders([
                    'x-rapidapi-key' => config('services.hotels_provider.key'),
                    'x-rapidapi-host' => config('services.hotels_provider.host'),
                ])->get('https://' . config('services.hotels_provider.host') . '/locations/v3/search', [
                    'q' => $query,
                    'locale' => 'en_US',
                    'langid' => '1033',
                    'siteid' => '300000001',
                ]);

                if ($response->failed()) {
                    Log::warning('Hotels4 region resolve failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'query' => $query,
                    ]);
                    return null;
                }

                return data_get($response->json(), 'sr.0.gaiaId');
            } catch (\Throwable $e) {
                Log::error('Hotels4 region resolve threw an exception', [
                    'message' => $e->getMessage(),
                    'query' => $query,
                ]);
                return null;
            }
        });
    }

    public function store(StoreHotelBookingRequest $request)
    {
        $validated = $request->validated();

        $hotelDetails = $this->fetchHotelDetails($validated['external_hotel_id']);

        if (! $hotelDetails) {
            return response()->json(['message' => 'Hotel details could not be verified.'], 422);
        }

        $price = data_get($hotelDetails, 'price');
        $currency = data_get($hotelDetails, 'currency');

        if ($price === null) {
            Log::error('Could not extract price from hotel details response', [
                'hotel_id' => $validated['external_hotel_id'],
            ]);
            return response()->json(['message' => 'Could not verify hotel pricing.'], 422);
        }

        $checkIn = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);

        $booking = bookings::create([
            'client_id' => Auth::id(),
            'type' => 'hotel',
            'provider' => 'rapidapi_hotels',
            'external_reference_id' => $validated['external_hotel_id'],
            'check_in_date' => $checkIn,
            'check_out_date' => $checkOut,
            'number_of_days' => $checkIn->diffInDays($checkOut),
            'number_of_bookings' => $validated['number_of_bookings'],
            'classes' => $validated['classes'],
            'status' => 'pending',
            'total_price' => $price,
            'currency' => $currency ?? 'USD',
            'details' => $hotelDetails,
        ]);

        return response()->json($booking, 201);
    }

    protected function fetchHotelDetails(string $externalHotelId): ?array
    {
        $cacheKey = 'hotel_details_' . $externalHotelId;

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($externalHotelId) {
            try {
                $response = Http::withHeaders([
                    'x-rapidapi-key' => config('services.hotels_provider.key'),
                    'x-rapidapi-host' => config('services.hotels_provider.host'),
                    'Content-Type' => 'application/json',
                ])->post('https://' . config('services.hotels_provider.host') . '/properties/v2/detail', [
                    'currency' => 'USD',
                    'eapid' => 1,
                    'locale' => 'en_US',
                    'siteId' => 300000001,
                    'propertyId' => $externalHotelId,
                ]);

                if ($response->failed()) {
                    Log::warning('Hotels4 detail fetch failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'hotel_id' => $externalHotelId,
                    ]);
                    return null;
                }

                $raw = $response->json();

                // NOTE: I haven't seen a real response from properties/v2/detail,
                // so this path is a best guess based on Hotels4's typical shape.
                // Log the raw response once and adjust this line to match reality:
                Log::info('Hotels4 detail raw response (remove after confirming path)', [
                    'hotel_id' => $externalHotelId,
                    'raw' => $raw,
                ]);

                $price = data_get($raw, 'data.propertyInfo.summary.propertyRates.lowRateInfo.total.units')
                    ?? data_get($raw, 'data.propertyInfo.summary.propertyRates.priceInAWeek.exactCurrentPrice.roundedAmount')
                    ?? data_get($raw, 'price');

                $currency = data_get($raw, 'data.propertyInfo.summary.propertyRates.lowRateInfo.total.currencyCode')
                    ?? data_get($raw, 'currency', 'USD');

                return [
                    'price' => $price,
                    'currency' => $currency,
                    'raw' => $raw,
                ];
            } catch (\Throwable $e) {
                Log::error('Hotels4 detail fetch threw an exception', [
                    'message' => $e->getMessage(),
                    'hotel_id' => $externalHotelId,
                ]);
                return null;
            }
        });
    }
}