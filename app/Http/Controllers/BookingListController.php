<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\BookingRepositoryInterface;
use Illuminate\Http\Request;

class BookingListController extends Controller
{
    public function __construct(protected BookingRepositoryInterface $bookingRepository) {}

    public function all(Request $request)
    {
        $bookings = $this->bookingRepository->findByUser(
            auth('api')->user()->client->id
        );

        return response()->json(['data' => $bookings]);
    }

    public function hotels(Request $request)
    {
        $bookings = $this->bookingRepository
            ->findByUser(auth('api')->user()->client->id)
            ->where('type', 'hotel')
            ->values();

        return response()->json(['data' => $bookings]);
    }

    public function flights(Request $request)
    {
        $bookings = $this->bookingRepository
            ->findByUser(auth('api')->user()->client->id)
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
