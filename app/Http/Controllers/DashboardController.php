<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileSettingsRequest;
use App\Repositories\Contracts\DashboardRepositoryInterface;

class DashboardController extends Controller
{
    protected DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository){
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getSavedTrips(Request $request){
        return response()->json(
            $this->dashboardRepository->getSavedTrips($request->user()->id)
        );
    }

    public function getFavouriteDestinations(Request $request){
        return response()->json(
            $this->dashboardRepository->getFavouriteDestinations($request->user()->id)
        );
    }

    public function getBookingHistory(Request $request){
        return response()->json(
            $this->dashboardRepository->getBookingHistory($request->user()->id)
        );
    }

    public function getProfileSettings(Request $request){
        return response()->json(
            $this->dashboardRepository->getProfileSettings($request->user()->id)
        );
    }

    public function updateProfileSettings(UpdateProfileSettingsRequest $request){
        return response()->json(
            $this->dashboardRepository->updateProfileSettings(
                $request->user()->id,
                $request->all()
            )
        );
    }

    public function getStatistics(Request $request){
        return response()->json(
            $this->dashboardRepository->getStatistics($request->user()->id)
        );
    }
}

