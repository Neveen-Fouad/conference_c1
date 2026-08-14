<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    protected function isOwner(User $user, Trip $trip): bool
    {
        return $trip->clients()
            ->where('user_id', $user->id)
            ->exists();
    }

    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Trip $trip): bool
    {
        if (!$trip->clients()->exists()) {
            return true;
        }
        return $this->isAdmin($user) || $this->isOwner($user, $trip);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function createViaAi(User $user): bool
    {
        return true;

    }

    public function update(User $user, Trip $trip): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $this->isAdmin($user) || $this->isOwner($user, $trip);
    }
}