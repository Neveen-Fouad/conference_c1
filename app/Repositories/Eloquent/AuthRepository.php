<?php

namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\JWTGuard;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user',
                'is_active' => true,
            ]);

            $client = Client::create([
                'latittude' => $data['latittude'],
                'long' => $data['long'],
                'phone' => $data['phone'],
                'birth_date' => $data['birth_date'],
                'user_id' => $user->id,
            ]);

            event(new Registered($user));

            DB::commit();

            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function login(array $credentials)
    {
        if (! $token = $this->guard()->attempt($credentials)) {
            return false;
        }

        return [
            'user' => $this->guard()->user(),
            'token' => $token,
        ];

    }

    public function logout()
    {
        $this->guard()->logout();

        return true;
    }

    public function verifyEmail(User $user)
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return true;
    }

    public function resetPassword(array $data)
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    }

    public function forgotPassword(array $data)
    {
        return Password::sendResetLink([
            'email' => $data['email'],
        ]);
    }

    // public function resetPassword(array $data){
    //     return Password::reset(
    //         $data,
    //         function (User $user, string $password){
    //             $user->forceFill([
    //                 'password' => Hash::make($password),
    //             ])->save();
    //         }
    //     );
    // }

    public function refresh()
    {
        return $this->guard()->refresh();
    }

    private function guard(): JWTGuard
    {
        $guard = auth('api');

        if (! $guard instanceof JWTGuard) {
            throw new \LogicException('The api guard must use the JWT driver.');
        }

        return $guard;
    }
}
