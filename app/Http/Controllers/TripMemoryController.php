<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripMemory;
use App\Services\TripMemoryService;
use Illuminate\Http\Request;

class TripMemoryController extends Controller
{
    public function __construct(private TripMemoryService $service) {}

    public function store(Request $request, Trip $trip)
    {
        $this->service->assertMember($trip);

        $request->validate([
            'type' => 'required|in:photo,note,voice',
            'file' => 'required_if:type,photo,voice|file|max:10240',
            'caption' => 'nullable|string|max:255',
            'note' => 'required_if:type,note|string',
        ]);

        $memory = $this->service->addMemory(
            $trip, $request->type, $request->note, $request->file('file'), $request->caption
        );

        return response()->json($memory, 201);
    }

    public function index(Trip $trip)
    {
        $this->service->assertMember($trip);

        return response()->json($this->service->getCapsule($trip));
    }

    public function destroy(Trip $trip, TripMemory $memory)
    {
        $this->service->deleteMemory($trip, $memory);

        return response()->json(null, 204);
    }
}
