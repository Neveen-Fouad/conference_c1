<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function getProfile(User $user)
    {
        return Cache::remember(
            "profile_{$user->id}",
            now()->addMinutes(10),
            fn () => $this->withClientId($user)
        );
    }

    public function updateProfile(User $user, array $data)
    {
        $user->update($data);
        Cache::forget("profile_{$user->id}");

        return $this->withClientId($user->fresh());
    }

    public function updatePassword(User $user, array $data)
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update(['password' => Hash::make($data['password'])]);
        Cache::forget("profile_{$user->id}");

        return true;
    }

    private function withClientId(User $user): User
    {
        $user->load('client');
        $user->setAttribute('client_id', $user->client?->id);

        return $user;
    }
}
