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

    /**
     * POST /api/transportation/tips
     *
     * Body:
     * {
     *   "origin": { "lat": 30.0444, "lng": 31.2357 },
     *   "destinations": [
     *     { "id": "restaurant_123", "lat": 30.05, "lng": 31.24 },
     *     { "id": "attraction_456", "lat": 30.06, "lng": 31.23 }
     *   ],
     *   "profile": "foot-walking"   // or "driving-car", defaults to foot-walking
     * }
     *
     * Response:
     * {
     *   "data": {
     *     "restaurant_123": {
     *       "destination_id": "restaurant_123",
     *       "profile": "foot-walking",
     *       "duration_minutes": 12,
     *       "distance_meters": 950,
     *       "label": "12 min walk",
     *       "is_estimate": false
     *     },
     *     ...
     *   }
     * }
     *
     * "is_estimate": true means ORS was unreachable and the value is a
     * haversine-based approximation rather than a real routed time.
     */
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
            'profile' => ['sometimes', 'in:foot-walking,driving-car'],
        ]);

        $profile = $validated['profile'] ?? TransportationService::PROFILE_WALKING;

        $tips = $this->transportationService->getTravelTimes(
            $validated['origin'],
            $validated['destinations'],
            $profile
        );

        return response()->json(['data' => $tips]);
    }
}