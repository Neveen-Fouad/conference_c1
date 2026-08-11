<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherServices
{
    protected string $key;
    protected string $baseUrl;

public function __construct()
{
    $this->key = config('services.weatherapi.key');
    $this->baseUrl = config('services.weatherapi.base_url');
}

    public function getCurrentWeather(string $city, ?string $countryCode = null): ?array
    {
        $query = $countryCode ? "{$city},{$countryCode}" : $city;

        return Cache::remember("weather_current_" . md5($query), now()->addMinutes(30), function () use ($query) {
            $response = Http::get("{$this->baseUrl}/current.json", ['key' => $this->key,'q' => $query,'aqi' => 'no',]);

            return $response->successful() ? $response->json() : null;
        });
    }
    public function getForecast(string $city, ?string $countryCode = null): ?array
    {
        $query = $countryCode ? "{$city},{$countryCode}" : $city;

        return Cache::remember("weather_forecast_" . md5($query), now()->addHours(1), function () use ($query) {
               $response = Http::get("{$this->baseUrl}/forecast.json", ['key' => $this->key,'q' => $query,'days' => 14,'aqi' => 'no','alerts' => 'no',]);

            return $response->successful() ? $response->json() : null;
        });
    }
}