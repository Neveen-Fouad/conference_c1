<?php

namespace App\Repositories\Eloquent;


use App\Models\trip;
use App\Interfaces\TripRepositoryInterface;

class TripRepository implements TripRepositoryInterface
{
    /**
     * Create a new class instance.
     */
 public function __construct(protected trip $model) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
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
        $query->where('clients.id', $userId);
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
            'ai_generated_trips' => $this->model->where('is_ai_generated', true)->count()
        ];
    }
}
