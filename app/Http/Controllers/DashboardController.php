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
        $savedTrips =$this->dashboardRepository->getSavedTrips($request);

        return response()->json([
            "message" => "data",
                "data" => [
                    "savedTrips" => $savedTrips
                ]
            ]

      );
    }

    public function getFavouriteDestinations(Request $request){
        $getFavouriteDestinations= $this->dashboardRepository->getFavouriteDestinations($request->user()->id);

        return response()->json([
            "message" => "data",
                "data" => [
                    "favouriteDestinations" => $getFavouriteDestinations
                ]
            ]
        );
    }

    public function getBookingHistory(Request $request){
        $getBookingHistory= $this->dashboardRepository->getBookingHistory($request->user()->id);

        return response()->json([
            "message" => "data",
                "data" => [
                    "bookingHistory" => $getBookingHistory
                ]
            ]
        );
    }

    public function getProfileSettings(Request $request){
        $getProfileSettings= $this->dashboardRepository->getProfileSettings($request->user()->id);
        return response()->json([
            "message" => "data",
                "data" => [
                    "profileSettings" => $getProfileSettings
                ]
            ]

        );
    }

    public function updateProfileSettings(UpdateProfileSettingsRequest $request){
        $updateProfileSettings = $this->dashboardRepository->updateProfileSettings($request->user()->id, $request->all());
        return response()->json([
            "message" => "data",
                "data" => [
                    "profileSettings" => $updateProfileSettings
                ]
            ]
            )
        ;
    }

    public function getStatistics(Request $request){
        $GetStatistics = $this->dashboardRepository->getStatistics($request->user()->id);
        return response()->json([
            "message" => "data",
                "data" => [
                    "statistics" => $GetStatistics
                ]
            ]
        );
    }
}

