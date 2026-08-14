<?php

namespace App\Http\Controllers;

use App\Services\CountryServices;
use App\Services\PlaceServices;
use App\Services\WeatherServices;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function __construct(
        protected CountryServices $countryApi,
        protected WeatherServices $weather,
        protected PlaceServices $places
    ) {}

    public function index()
    {
        $countries = $this->countryApi->getAllCountries();

        return response()->json([
            'countries' => $countries,

        ]);
        // return view('explore', [
        //     'countries' => $countries,
        //     'interests' => Interests::all(['id', 'name']),
        // ]);
    }

    public function destinationData(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100',
            'country_code' => 'nullable|string|max:5',
        ]);

        $city = $request->input('city');
        $countryCode = $request->input('country_code');

        $weather = $this->weather->getCurrentWeather($city, $countryCode);
        $attractions = $this->places->getAttractions($city);

        return response()->json([
            'weather' => $weather,
            'attractions' => $attractions['results'],
        ]);
    }
}
