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
            'average_trip_duration' => trip::selectRaw('AVG(DATEDIFF(end_date, start_date)) as avg_duration')->value('avg_duration'),
        ];
    }

    public function findByUserId(int $userId)
    {
        return $this->model->whereHas('clients', function ($query) use ($userId) {
            $query->where('clients.user_id', $userId);
        })->with('details')->get();
    }

    public function delete($id)
    {
        $trip = $this->findById($id);
        
        // Detach pivot table relationships
        $trip->clients()->detach();
        
        // Delete related details
        $trip->details()->delete();
        
        // Finally, delete the trip itself
        return $trip->delete();
    }

}