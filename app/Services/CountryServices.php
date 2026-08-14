<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CountryServices
{
    protected string $baseUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = (string) config('services.countries.base_url');
        $this->apiKey = (string) config('services.countries.api_key');
    }

    public function getCountryInfo(string $countryName): ?array
    {
        if ($this->apiKey === '') {
            return $this->getCountryFromBackup($countryName);
        }

        return Cache::remember(
            'country_'.md5(strtolower($countryName)),
            now()->addDay(),
            function () use ($countryName) {

                $response = Http::withToken($this->apiKey)
                    ->get(
                        "{$this->baseUrl}/countries/v5/names.common/".urlencode($countryName),
                        [
                            'pretty' => 1,
                            'response_fields' => 'names.common,codes.alpha_2,codes.alpha_3,capital,currencies,languages,flag.emoji',
                        ]
                    );

                if ($response->successful()) {
                    $country = $response->json('data.objects.0');

                    if ($country) {
                        return $this->formatCountry($country);
                    }
                }

                return $this->getCountryFromBackup($countryName);
            }
        );
    }

    public function getAllCountries(): array
    {
        if ($this->apiKey === '') {
            return $this->getAllCountriesFromBackup();
        }

        return Cache::remember(
            'countries_all',
            now()->addWeek(),
            function () {

                $allCountries = [];

                foreach ([0, 100, 200] as $offset) {

                    $response = Http::withToken($this->apiKey)
                        ->get(
                            "{$this->baseUrl}/countries/v5",
                            [
                                'limit' => 100,
                                'offset' => $offset,
                                'response_fields' => 'names.common,codes.alpha_2,codes.alpha_3,capital,currencies,languages,flag.emoji',
                            ]
                        );

                    if (! $response->successful()) {
                        return $this->getAllCountriesFromBackup();
                    }

                    $countries = $response->json('data.objects') ?? [];

                    $allCountries = array_merge(
                        $allCountries,
                        $countries
                    );
                }

                return $this->formatCountries($allCountries);
            }
        );
    }

    private function getCountryFromBackup(string $countryName): ?array
    {
        $backup = $this->loadBackup();

        $country = collect($backup['data']['objects'] ?? [])
            ->first(function ($country) use ($countryName) {
                return strtolower(
                    $country['names']['common'] ?? ''
                ) === strtolower($countryName);
            });

        if (! $country) {
            return null;
        }

        return $this->formatCountry($country);
    }

    private function getAllCountriesFromBackup(): array
    {
        $backup = $this->loadBackup();

        return $this->formatCountries(
            $backup['data']['objects'] ?? []
        );
    }

    private function loadBackup(): array
    {
        $path = base_path(
            'database/data/external-apis/countries.json'
        );

        if (! file_exists($path)) {
            return [];
        }

        $backup = json_decode(
            file_get_contents($path),
            true
        );

        return is_array($backup) ? $backup : [];
    }

    private function formatCountry(array $country): array
    {
        return [
            'name' => $country['names']['common'] ?? null,
            'code' => $country['codes']['alpha_3'] ?? null,
            'code2' => $country['codes']['alpha_2'] ?? null,
            'capital' => $country['capital'] ?? null,
            'currency' => $country['currencies'][0]['code'] ?? null,
            'language' => $country['languages'][0]['name'] ?? null,
            'flag' => $country['flag']['emoji'] ?? null,
        ];
    }

    private function formatCountries(array $countries): array
    {
        return collect($countries)
            ->map(fn ($country) => $this->formatCountry($country))
            ->filter(
                fn ($country) => $country['name'] && $country['code']
            )
            ->sortBy('name')
            ->values()
            ->toArray();
    }
}
