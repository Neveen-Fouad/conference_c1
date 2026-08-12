<?php

namespace App\Policies;

use App\Models\trip;
use App\Models\User;

class TripPolicy
{

    protected function isOwner(User $user, trip $trip): bool
    {
        return $trip->clients()
            ->where('user_id', $user->id)
            ->exists();
    }

    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, trip $trip): bool
    {
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
    public function update(User $user, trip $trip): bool
    {
        if ($trip->clients()->exists()) {
            return $this->isOwner($user, $trip);
        }

        return $this->isAdmin($user);
    }


    public function delete(User $user, trip $trip): bool
    {
        if ($trip->clients()->exists()) {
            return $this->isOwner($user, $trip);
        }
        return $this->isAdmin($user);
    }
}