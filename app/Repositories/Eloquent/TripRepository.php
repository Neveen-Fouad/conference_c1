<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\TripRepositoryInterface;
use App\Models\trip;

class TripRepository implements TripRepositoryInterface
{
    /**
     * Create a new class instance.
     */
 public function __construct(protected trip $model) {}

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
        $appointment = $this->findById($id);
        $appointment->update($data);

        return $appointment;
    }

    public function delete(int $id): bool
    {
        return $this->findById($id)->delete();
    }
}
