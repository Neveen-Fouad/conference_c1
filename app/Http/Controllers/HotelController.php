<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelSearchRequest;
use App\Services\HotelService;

class HotelController extends Controller
{
    public function __construct(protected HotelService $hotelService)
    {
    }

    public function index(HotelSearchRequest $request)
    {
        $hotels = $this->hotelService->listHotels($request->validated());

        if (empty($hotels)) {
            return response()->json([
                'message' => 'No hotels found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'data' => $hotels
        ]);
    }

    public function show(string $hotel)
    {
        $details = $this->hotelService->getHotelDetails($hotel);

        return $details
            ? response()->json($details)
            : response()->json([
                'message' => 'Not found'
            ], 404);
    }
}