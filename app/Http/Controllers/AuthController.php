<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    protected AuthRepositoryInterface $authRepository;
    protected NotificationService $notificationService;

    public function __construct(AuthRepositoryInterface $authRepository, NotificationService $notificationService){
        $this->authRepository = $authRepository;
        $this->notificationService = $notificationService;
    }

    public function login(LoginRequest $request): JsonResponse{
        $data = $this->authRepository->login($request->validated());
        if (!$data){
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = auth('api')->user();
        $client = \App\Models\Client::where('user_id', $user->id)->first();
        
        if ($client) {
            $this->notificationService->createNotification([
                'client_id'   => $client->id,
                'type'        => 'login',
                'description' => 'A new login to your account was detected.',
            ]);
        }

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'token' => [
                'token' => $data['token'],
            ]['token'],
            ],
        ]);
    }
    public function logout(): JsonResponse{
        $this->authRepository->logout();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse{
        $request->fulfill();

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }

    // public function resendVerificationEmail(Request $request): JsonResponse{
    //     if ($request->user()->hasVerifiedEmail()){
    //         return response()->json([
    //             'message' => 'Email is already verified.',
    //         ], 400);
    //     }

    //     $this->authRepository->resendVerificationEmail($request->user());

    //     return response()->json([
    //         'message' => 'Verification email sent successfully.',
    //     ]);
    // }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse{
        $this->authRepository->forgotPassword($request->only('email'));

        return response()->json([
            'message' => 'Password reset link sent successfully.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse{
        $status = $this->authRepository->resetPassword($request->only(
    'email',
    'token',
    'password',
    'password_confirmation'
));

if ($status !== Password::PASSWORD_RESET) {
    return response()->json([
        'message' => __($status),
    ], 400);
}

return response()->json([
    'message' => 'Password reset successfully.',
]);
}

public function refresh(): JsonResponse{
    $token = $this->authRepository->refresh();

    return response()->json([
        'message' => 'Token refreshed successfully.',
        'data' => [
            'token' => $token,
        ],
    ]);
}
}

 
    