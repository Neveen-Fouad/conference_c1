<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Repositories\Contracts\ProfileRepositoryInterface;

class ProfileController extends Controller
{
    protected ProfileRepositoryInterface $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository){
        $this->profileRepository = $profileRepository;
    }
    public function getProfile(): JsonResponse{
        $profile = $this->profileRepository->getProfile(auth()->user());

        return response()->json([

            'message' => 'Profile retrieved successfully.',
            'data' => [
                'profile' => $profile
            ],
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse{
         $profile = $this->profileRepository->updateProfile(
            auth()->user(),
            $request->validated()
         );

         return response()->json([

            'message' => 'Profile updated successfully.',
            'data' => [
                'profile' => $profile
            ],
         ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse{
        $this->profileRepository->updatePassword(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Password updated successfully.',
         ]);
    }
}
