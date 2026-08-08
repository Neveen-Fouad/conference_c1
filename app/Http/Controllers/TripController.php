<?php

namespace App\Http\Controllers;

use App\Interfaces\TripRepositoryInterface;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Requests\StoreTripRequest;


class TripController extends Controller
{
    //
    protected $tripRepository;
    public function __construct(TripRepositoryInterface $tripRepository){
        $this->tripRepository = $tripRepository;
    
    }

    public function index(){
        return response()->json(
            $this->tripRepository->getAll()
        );
    }

   public function update(UpdateTripRequest $request, $id)
{
    return response()->json(
        $this->tripRepository->update($id, $request->validated())
    );
}

    public function destroy($id){
        return response()->json(
            $this->tripRepository->delete($id)
        );
    }

    public function statistics(){
        return response()->json(
            $this->tripRepository->statistics()
        );
    }

    public function store(StoreTripRequest $request){
        $trip = $this->tripRepository->create($request->validated());
        return response()->json($trip, 201);

    }

    public function getTripsByUserId($userId){
        $trips = $this->tripRepository->findByUserId($userId);
        return response()->json($trips);
    }

}


