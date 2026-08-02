<?php
namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\Contracts\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface{

    public function register(array $data){
        DB::beginTransaction();

        try{
            $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role' => 'client',
            'is_active'  => true,
        ]);

        event(new Registered($user));

        DB::commit();

        return $user;

        }catch (\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function login(array $credentials){
        if (!$token = JWTAuth::attempt($credentials)){
            return false;
        }

        return[
            'user' => auth()->user(),
            'token' => $token,
        ];

    }
    
    public function logout(){
        JWTAuth::invalidate(JWTAuth::getToken());

        return true;
    }

    public function verifyEmail(User $user){
        if (!$user->hasVerifiedEmail()){
            $user->markEmailAsVerified();
        }
        return true;
    }

    public function resendVerificationEmail(User $user){
        if (!$user->hasVerifiedEmail()){
            $user->sendEmailVerificationNotification();
        }
        return true;
    }

    public function forgotPassword(array $data){
        return Password::sendResetLink([
            'email' => $data['email'],
        ]);
    }

    public function resetPassword(array $data){
        return Password::reset(
            $data,
            function (User $user, string $password){
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
    }
}