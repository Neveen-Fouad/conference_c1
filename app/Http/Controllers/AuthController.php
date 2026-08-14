<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    protected AuthRepositoryInterface $authRepository;

    protected NotificationService $notificationService;

    public function __construct(AuthRepositoryInterface $authRepository, NotificationService $notificationService)
    {
        $this->authRepository = $authRepository;
        $this->notificationService = $notificationService;
    }


    public function register(RegisterRequest $request): JsonResponse{
        $data = $this->authRepository->register($request->validated());

        $token = auth('api')->login($data);

        return response()->json([
            'message' => 'User registered successfully. Please verify your email.',
            'data' => [
                'token' => $token,
            ],
        ], 201);
    }

  
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authRepository->login($request->validated());
        if (! $data) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = auth('api')->user()->load('client');
        $client = $user->client;

        if ($client) {
            $this->notificationService->createNotification([
                'client_id' => $client->id,
                'type' => 'login',
                'description' => 'A new login to your account was detected.',
            ]);
        }

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'token' => $data['token'],
                'user' => [
                    'id' => $user->id,
                    'client_id' => $client?->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

  

    public function logout(): JsonResponse
    {
        $this->authRepository->logout();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function verifyEmail(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }

    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email is already verified.',
            ], 409);
        }

        $this->authRepository->resendVerificationEmail($user);

        return response()->json([
            'message' => 'Verification email sent successfully.',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authRepository->forgotPassword($request->only('email'));

        return response()->json([
            'message' => 'Password reset link sent successfully.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
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

    public function refresh(): JsonResponse
    {
        $token = $this->authRepository->refresh();

        return response()->json([
            'message' => 'Token refreshed successfully.',
            'data' => [
                'token' => $token,
            ],
        ]);
    }
}
