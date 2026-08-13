<?php
namespace App\Repositories\Eloquent;

use App\Models\Client;
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
                'role'       => 'user',
                'is_active'  => true,
            ]);

            $client = Client::create([
                'latittude'  => $data['latittude'],
                'long'       => $data['long'],
                'phone'      => $data['phone'],
                'birth_date' => $data['birth_date'],
                'user_id'    => $user->id
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

    public function resetPassword(array $data){
    return Password::reset(
        $data,
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
        }
    );
}

    public function forgotPassword(array $data){
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
    
    public function refresh(){
    return JWTAuth::refresh(JWTAuth::getToken());
}
public function resendVerificationEmail(User $user){
        if (!$user->hasVerifiedEmail()){
            $user->sendEmailVerificationNotification();
        }
        return true;
    }


}