<?php

namespace App\Repositories\Eloquent;


use App\Models\trip;
use App\Repositories\Contracts\TripRepositoryInterface;

class TripRepository implements TripRepositoryInterface
{
    /**
     * Create a new class instance.
     */
 public function __construct(protected Trip $model) {}

    public function findAll()
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
        return $this->model->where('user_id', $userId)->get();
    }
}
