<?php

namespace App\Repositories\Eloquent;



use App\Models\Booking;
use App\Interfaces\TripRepositoryInterface;
use App\Models\Trip;

class TripRepository implements TripRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Trip $model) {}

    public function getAll()
    {
        return $this->model->with('details')->get();
    }

    public function findById(int $id)
    {
        return $this->model->with('details')->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $trip = $this->findById($id);
        $trip->update($data);

        return $trip;
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }

    public function findByUserId(int $userId)
    {
        return $this->model->whereHas('clients', function ($query) use ($userId) {
            $query->where('clients.user_id', $userId);
        })->with('details')->get();
    }

    public function getTripDetails(int $tripId)
    {
        return $this->findById($tripId)->details;
    }

    public function statistics()
    {
        return [
            'total_trips' => $this->model->count(),
            'monthly_trips' => $this->model->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            // 'favorite_trips' => $this->model->where('is_fav', true)->count(),
            'average_budget' => $this->model->avg('budget'),
            'average_trip_duration' => $this->model->avg('number_of_days'),
        ];
    }

    public function getPreMadeTrips()
{
    return $this->model->doesntHave('clients')->with('details')->get();
}

    public function bookTrip(int $tripId, int $clientId)
    {
        $trip = $this->findById($tripId);

        $alreadyBooked = Booking::where('client_id', $clientId)
            ->where('type', 'trip')
            ->where('external_reference_id', (string) $tripId)
            ->exists();

        if ($alreadyBooked) {
            throw new \Exception('You have already booked this trip.');
        }

        return Booking::create([
            'client_id' => $clientId,
            'type' => 'trip',
            'booking_type' => 'trip',
            'provider' => 'Journovo',
            'provider_name' => 'Journovo Trips',
            'external_reference_id' => (string) $trip->id,
            'number_of_days' => $trip->number_of_days,
            'number_of_bookings' => $trip->number_of_travels ?? 1,
            'status' => 'pending',
            'check_in_date' => $trip->start_date,
            'check_out_date' => \Carbon\Carbon::parse($trip->start_date)->addDays(max(0, $trip->number_of_days - 1))->toDateString(),
            'booking_date' => now()->toDateString(),
            'total_price' => $trip->estimated_expenses ?? $trip->budget ?? 0,
            'currency' => 'USD',
            'details' => [
                'trip_id' => $trip->id,
                'destination' => $trip->destination,
                'style' => $trip->style,
            ],
        ]);
    }
}
