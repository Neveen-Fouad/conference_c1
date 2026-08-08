<?php

namespace App\Http\Controllers;

use App\Services\CountryServices;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    protected CountryServices $countryService;

    public function __construct(CountryServices $countryService)
    {
        $this->countryService = $countryService;
    }

    public function index(): JsonResponse
    {
        return response()->json(
            $this->countryService->getAllCountries()
        );
    }
    public function show(string $country): JsonResponse
    {
        $countryData = $this->countryService->getCountryInfo($country);

        if (!$countryData) {
            return response()->json([
                'message' => 'Country not found'
            ], 404);
        }

        return response()->json($countryData);
    }
}
