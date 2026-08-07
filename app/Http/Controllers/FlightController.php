<?php
namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
use App\Http\Requests\FlightDetailsRequest;
use App\Services\FlightService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function __construct(protected FlightService $flightService)
    {
    }

    public function index(FlightSearchRequest $request)
    {
        return response()->json($this->flightService->searchFlights($request->validated()));
    }

    public function show(FlightDetailsRequest $request)
{
    $results = $this->flightService->searchFlights([
        'origin' => $request->origin,
        'destination' => $request->destination,
        'date' => $request->date,
        'adults' => $request->adults ?? 1,
    ]);

    return $results ? response()->json($results) : response()->json(['message' => 'No flights found'], 404);
}
}