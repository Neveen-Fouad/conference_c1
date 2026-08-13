<?php

namespace App\Http\Controllers;

use App\Http\Requests\RestaurantDetailRequest;
use App\Http\Requests\RestaurantListRequest;
use App\Services\RestaurantService;

class RestaurantController extends Controller
{
    public function __construct(protected RestaurantService $restaurantService) {}

    public function index(RestaurantListRequest $request)
    {
        return response()->json($this->restaurantService->listRestaurants($request->validated()));
    }

    public function show(RestaurantDetailRequest $request)
    {
        $details = $this->restaurantService->getRestaurantDetails(
            $request->validated()['id']
        );

        return $details ? response()->json($details) : response()->json(['message' => 'Not found'], 404);
    }
}
