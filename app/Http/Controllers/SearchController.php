<?php

namespace App\Http\Controllers;

use App\services\SearchService;
use App\Http\Requests\HotelSearchRequest;

class SearchController extends Controller
{
    public function __construct(protected SearchService $searchService)
    {
    }

    public function searchHotels(HotelSearchRequest $request)
    {
        $hotels = $this->searchService->searchHotels($request->validated());
        
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

}
