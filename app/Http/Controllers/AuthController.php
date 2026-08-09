<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected AuthRepositoryInterface $authRepository;


    public function __construct(AuthRepositoryInterface $authRepository){
        $this->authRepository = $authRepository;
    }

    public function register(RegisterRequest $request): JsonResponse{
    public function register(RegisterRequest $request): JsonResponse{
        $user = $this->authRepository->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully. Please verify your email.',
            'data' => [
                'token' => $user->token,
            ],
            'data' => [
                'token' => $user->token,
            ],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse{
        $data = $this->authRepository->login($request->validated());
        if (!$data){
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return response()->json([
            'message' => 'Login successful.',
            'data' => [
                'token' => [
                'token' => $data->token,
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

    public function resendVerificationEmail(Request $request): JsonResponse{
        if ($request->user()->hasVerifiedEmail()){
            return response()->json([
                'message' => 'Email is already verified.',
            ], 400);
        }

        $this->authRepository->resendVerificationEmail($request->user());

        return response()->json([
            'message' => 'Verification email sent successfully.',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse{
        $this->authRepository->forgotPassword($request->only('email'));

        return response()->json([
            'message' => 'Password reset link sent successfully.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse{
        $this->authRepository->resetPassword($request->only(
            'email',
            'token',
            'password',
            'password_confirmation'
        ));

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
