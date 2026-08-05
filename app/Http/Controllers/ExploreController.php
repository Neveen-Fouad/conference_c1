<?php

namespace App\Http\Controllers;

use App\Services\CountryServices;
use App\Services\PlaceServices;
use App\Services\WeatherServices;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    protected array $interests = [
        ['slug' => 'hiking',     'name' => 'Hiking'],
        ['slug' => 'beaches',    'name' => 'Beaches'],
        ['slug' => 'camping',    'name' => 'Camping'],
        ['slug' => 'museums',    'name' => 'Museums'],
        ['slug' => 'historical', 'name' => 'Historical'],
        ['slug' => 'shopping',   'name' => 'Shopping'],
        ['slug' => 'adventure',  'name' => 'Adventure'],
    ];

    public function __construct(
        protected CountryServices $countryApi,
        protected WeatherServices $weather,
        protected PlaceServices $places
    ) {}

    public function index()
    {
        $countries = $this->countryApi->getAllCountries();

        return view('explore', [
            'countries' => $countries,
            'interests' => $this->interests,
        ]);
    }

    public function destinationData(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:100',
            'country_code' => 'nullable|string|max:5',
            'interest' => 'nullable|string',
        ]);

        $city = $request->input('city');
        $countryCode = $request->input('country_code');
        $interest = $request->input('interest');

        $weather = $this->weather->getCurrentWeather($city, $countryCode);
        $attractions = $this->places->getAttractions($city, $interest);
        $restaurants = $this->places->getRestaurants($city);

        return response()->json([
            'weather' => $weather,
            'attractions' => $attractions['results'],
            'restaurants' => $restaurants['results'],
        ]);
    }
}