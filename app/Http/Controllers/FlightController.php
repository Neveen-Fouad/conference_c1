<?php
namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
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

    public function show(string $flight)
    {
        $details = $this->flightService->getFlightDetails($flight);
        return $details ? response()->json($details) : response()->json(['message' => 'Not found'], 404);
    }
}