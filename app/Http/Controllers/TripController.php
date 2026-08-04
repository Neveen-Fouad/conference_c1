<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Repositories\Contracts\TripRepositoryInterface;
use Illuminate\Http\Request;

class TripController extends Controller
{
      public function __construct(
        protected TripRepositoryInterface $trips 
    ) {}

    public function index(){
        $trips = $this->trips->findAll();
        return response()->json($trips);
    }

    public function store(StoreTripRequest $request){
        $trip = $this->trips->create($request->validated());
        return response()->json($trip, 201);
    }
    public function show($id){
        $trip = $this->trips->findById($id);
        return response()->json($trip);
    }
    public function update(UpdateTripRequest $request, $id){
        $trip = $this->trips->update($id, $request->validated());
        return response()->json($trip);
    }
    public function destroy($id){
        $this->trips->delete($id);
        return response()->json(null, 204);
    }
    public function getTripsByUserId($userId){
        $trips = $this->trips->findByUserId($userId);
        return response()->json($trips);
    }
}
