<?php

namespace App\Http\Controllers;

use App\Services\HotelService;
use App\Http\Requests\HotelDetailsRequest;

class HotelController extends Controller
{
    public function __construct(protected HotelService $hotelService)
    {
    }


    public function show(HotelDetailsRequest $request)
    {
        $details = $this->hotelService->getHotelDetails($request->hotel_id);

        return $details
            ? response()->json($details)
            : response()->json([
                'message' => 'Not found'
            ], 404);
    }
}