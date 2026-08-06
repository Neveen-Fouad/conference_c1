<?php
namespace App\Http\Controllers;

use App\Http\Requests\RestaurantSearchRequest;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(protected RestaurantService $restaurantService)
    {
    }

    public function index(RestaurantSearchRequest $request)
    {
        return response()->json($this->restaurantService->listRestaurants($request->validated()));
    }

    public function show(string $restaurant)
    {
        $details = $this->restaurantService->getRestaurantDetails($restaurant);
        return $details ? response()->json($details) : response()->json(['message' => 'Not found'], 404);
    }
}