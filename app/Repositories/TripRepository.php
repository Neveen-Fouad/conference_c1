<?php

namespace App\Repositories;

use App\Models\trip;
use App\Interfaces\TripRepositoryInterface;

class TripRepository extends BaseRepository implements TripRepositoryInterface
{
    public function __construct(trip $trip)
    {
        parent::__construct($trip);
    }

    public function statistics()
    {
        return [
            'total_trips' => trip::count(),
            'monthly_trips' => trip::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'favorite_trips' => trip::where('is_fav', true)->count(),
            'average_budget' => trip::avg('budget'),
            'average_trip_duration' => trip::avg('number_of_days'),
        ];
    }

}