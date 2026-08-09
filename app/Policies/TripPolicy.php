<?php

namespace App\Policies;

use App\Models\trip;
use App\Models\User;

class TripPolicy
{
    /**
     * Helper: does this trip belong to this user (via clients pivot)?
     */
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

    /**
     * View a single trip.
     * - Owner can view their own trip.
     * - Admin can view any trip.
     */
    public function view(User $user, trip $trip): bool
    {
        return $this->isAdmin($user) || $this->isOwner($user, $trip);
    }

    /**
     * Create a trip MANUALLY (non-AI). Admin only.
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Create a trip VIA AI. Regular users only (generating for themselves).
     * Call this from AiTripController before generateTrip() runs.
     */
    public function createViaAi(User $user): bool
    {
        return !$this->isAdmin($user);
    }

    /**
     * Update a trip.
     * - Owner can update their own trip, but ONLY if it was AI-generated.
     * - Admin can update trips, but ONLY if they were created manually
     *   (never touches user-generated AI trips).
     */
    public function update(User $user, trip $trip): bool
    {
        if ($this->isAdmin($user)) {
            return !$trip->is_ai_generated;
        }

        return $this->isOwner($user, $trip) && $trip->is_ai_generated;
    }

    /**
     * Delete a trip.
     * - Owner can delete their own trip (any of their trips).
     * - Admin can delete only manually-created trips.
     */
    public function delete(User $user, trip $trip): bool
    {
        if ($this->isAdmin($user)) {
            return !$trip->is_ai_generated;
        }

        return $this->isOwner($user, $trip);
    }
}