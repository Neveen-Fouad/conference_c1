<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Http\Request;

class BookingListController extends Controller
{
    public function __construct(protected BookingRepositoryInterface $bookingRepository) {}

    public function all(Request $request)
    {
        $clientId = auth('api')->user()->client?->id;
        abort_unless($clientId, 403, 'User does not have a client profile.');

        $bookings = $this->bookingRepository->findByUser($clientId);

        return response()->json(['data' => $bookings]);
    }

    public function hotels(Request $request)
    {
        $clientId = auth('api')->user()->client?->id;
        abort_unless($clientId, 403, 'User does not have a client profile.');

        $bookings = $this->bookingRepository
            ->findByUser($clientId)
            ->where('type', 'hotel')
            ->values();

        return response()->json(['data' => $bookings]);
    }

    public function flights(Request $request)
    {
        $clientId = auth('api')->user()->client?->id;
        abort_unless($clientId, 403, 'User does not have a client profile.');

        $bookings = $this->bookingRepository
            ->findByUser($clientId)
            ->where('type', 'flight')
            ->values();

        return response()->json(['data' => $bookings]);
    }

    public function adminAll(Request $request, int $clientId)
    {
        $bookings = $this->bookingRepository->findByUser($clientId);

        return response()->json(['data' => $bookings]);
    }

    public function adminHotels(Request $request, int $clientId)
    {
        $bookings = $this->bookingRepository
            ->findByUser($clientId)
            ->where('type', 'hotel')
            ->values();

        return response()->json(['data' => $bookings]);
    }

    public function adminFlights(Request $request, int $clientId)
    {
        $bookings = $this->bookingRepository
            ->findByUser($clientId)
            ->where('type', 'flight')
            ->values();

        return response()->json(['data' => $bookings]);
    }
}
