<?php

namespace App\Http\Controllers;

use App\Services\TransportationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransportationController extends Controller
{
    public function __construct(protected TransportationService $transportationService)
    {
    }

    public function tips(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin' => ['required', 'array'],
            'origin.lat' => ['required', 'numeric', 'between:-90,90'],
            'origin.lng' => ['required', 'numeric', 'between:-180,180'],
            'destinations' => ['required', 'array', 'min:1', 'max:200'],
            'destinations.*.id' => ['required'],
            'destinations.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'destinations.*.lng' => ['required', 'numeric', 'between:-180,180'],
        ]);


        $tips = $this->transportationService->getSmartTravelTimes(
            $validated['origin'],
            $validated['destinations']
        );

        return response()->json(['data' => $tips]);
    }
}