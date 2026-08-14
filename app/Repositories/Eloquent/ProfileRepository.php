<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use Illuminate\Validation\ValidationException;

class ProfileRepository implements ProfileRepositoryInterface{
    public function getProfile(User $user){
        return Cache::remember(
            "profile_{$user->id}",
            now()->addMinutes(10),

            function () use ($user){
                return $user->load('client');
            }
        );
    }

    public function updateProfile(User $user, array $data){
        $user->update($data);

        Cache::forget("profile_{$user->id}");
        return $user->fresh()->load('client');
    }

    public function updatePassword(User $user, array $data){
        if (!Hash::check($data['current_password'], $user->password)){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        Cache::forget("profile_{$user->id}");
        return true;
    }

}
