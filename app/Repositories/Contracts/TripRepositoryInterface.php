<?php

namespace App\Repositories\Contracts;


interface TripRepositoryInterface
{
    public function findById(int $id);
    public function findAll();

    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByUserId(int $userId);
}
