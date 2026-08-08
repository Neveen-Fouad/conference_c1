<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CountryServices
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.countries.base_url');
        $this->apiKey = config('services.countries.api_key');
    }

    public function getCountryInfo(string $countryName): ?array
    {
        return Cache::remember("country_" . md5($countryName), now()->addDay(), function () use ($countryName) {

            $response = Http::withToken($this->apiKey)->get("{$this->baseUrl}/countries/v5/names.common/" . urlencode($countryName), ['pretty' => 1,'response_fields' => 'names.common,codes.alpha_2,codes.alpha_3,capital,currencies,languages,flag.emoji']);

            if (!$response->successful()) {
                return null;
            }

            $country = $response->json('data.objects.0');

            if (!$country) {
                return null;
            }

            return [
                'name'      => $country['names']['common'] ?? null,
                'code'      => $country['codes']['alpha_3'] ?? null,
                'code2'     => $country['codes']['alpha_2'] ?? null,
                'capital'   => $country['capital'] ?? null,
                'currency'  => $country['currencies'][0]['code'] ?? null,
                'language'  => $country['languages'][0]['name'] ?? null,
                'flag'      => $country['flag']['emoji'] ?? null,
            ];
        });
    }

    public function getAllCountries(): array
{
    return Cache::remember('countries_all', now()->addWeek(), function () {

        $allCountries = [];

        foreach ([0, 100, 200] as $offset) {

            $response = Http::withToken($this->apiKey)
                ->get("{$this->baseUrl}/countries/v5", [
                    'limit' => 100,
                    'offset' => $offset,
                    'response_fields' => 'names.common,codes.alpha_2,codes.alpha_3,capital,currencies,languages,flag.emoji',
                ]);

            if (!$response->successful()) {
                continue;
            }

            $countries = $response->json('data.objects') ?? [];

            $allCountries = array_merge($allCountries, $countries);
        }

        return collect($allCountries)
            ->map(function ($country) {
                return [
                    'name' => $country['names']['common'] ?? null,
                    'code' => $country['codes']['alpha_3'] ?? null,
                    'code2' => $country['codes']['alpha_2'] ?? null,
                    'capital' => $country['capital'] ?? null,
                    'currency' => $country['currencies'][0]['code'] ?? null,
                    'language' => $country['languages'][0]['name'] ?? null,
                    'flag' => $country['flag']['emoji'] ?? null,
                ];
            })
            ->filter(fn ($country) => $country['name'] && $country['code'])
            ->sortBy('name')
            ->values()
            ->toArray();
    });
}

}