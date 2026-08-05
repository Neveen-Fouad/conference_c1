<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Services\CountryServices;
use App\services\GroqService;
use App\Services\PlaceServices;
use App\Services\WeatherServices;
use Illuminate\Http\Request;

class AiTripController extends Controller
{

    public function __construct(
        protected GroqService $groqService,
        protected CountryServices $countryServices,
        protected PlaceServices $placeServices,
        protected WeatherServices $weatherServices
    ) {}
    function generateTrip(StoreTripRequest $request){
        $validatedData = $request->validated();
        $city = $validatedData['destination'];
        $countryInfo = $this->countryServices->getCountryInfo($city);
        dd($countryInfo);
        $trip = $this->groqService->MakeTrip($validatedData);
        return response()->json($trip);
    }
}
