<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\trip;
use App\Repositories\Contracts\TripRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TripController extends Controller
{
      public function __construct(
        protected TripRepositoryInterface $trips 
    ) {}

    public function index(){
        $user = auth()->user();
         if ($user->role === 'admin') {
        $trips = $this->trips->findAll();
    } else {
        $trips = $this->trips->findByUserId($user->id);
    }
        return response()->json($trips);
    }

    public function store(StoreTripRequest $request){ 
        Gate::authorize('create', trip::class);
        $trip = $this->trips->create($request->validated());
        return response()->json($trip, 201);
    }
    public function show($id){
        Gate::authorize('view', $trip = $this->trips->findById($id));
        return response()->json($trip);
    }
    public function update(UpdateTripRequest $request, $id){
        Gate::authorize('update', $trip = $this->trips->findById($id));
        $trip = $this->trips->update($id, $request->validated()); 
        return response()->json($trip);
    }
    public function destroy($id){
        Gate::authorize('delete', $trip = $this->trips->findById($id));
        $this->trips->delete($id);
        return response()->json(null, 204);
    }
    public function getTripsByUserId($userId){
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->id !== (int) $userId) {
        abort(403);
    }
        $trips = $this->trips->findByUserId($userId);
        return response()->json($trips);
    }
}
