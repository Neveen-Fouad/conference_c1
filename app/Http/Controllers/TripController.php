<?php

namespace App\Http\Controllers;

use App\Interfaces\TripRepositoryInterface;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Requests\StoreTripRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\trip;

class TripController extends Controller
{
    protected $tripRepository;
    
    public function __construct(TripRepositoryInterface $tripRepository){
        $this->tripRepository = $tripRepository;
    }

    public function index(){
        $user = Auth::user();
        if ($user->role === 'admin') {
            $trips = $this->tripRepository->getAll();
        } else {
            $trips = $this->tripRepository->findByUserId($user->id);
        }
        return response()->json($trips);
    }

    public function show($id){
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('view', $trip);
        return response()->json($trip);
    }

    public function store(StoreTripRequest $request){
        Gate::authorize('create', trip::class);
        $trip = $this->tripRepository->create($request->validated());
        return response()->json($trip, 201);
    }

    public function update(UpdateTripRequest $request, $id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('update', $trip);
        
        return response()->json(
            $this->tripRepository->update($id, $request->validated())
        );
    }

    public function destroy($id){
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('delete', $trip);
        
        return response()->json(
            $this->tripRepository->delete($id)
        );
    }

    public function getTripsByUserId($userId){
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id != $userId) {
            abort(403, 'Unauthorized action.');
        }
        
        $trips = $this->tripRepository->findByUserId($userId);
        return response()->json($trips);
    }

    public function getTripDays($id){
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('view', $trip);
        
        $details = $this->tripRepository->getTripDetails($id);
        return response()->json($details);
    }

    public function statistics(){
        return response()->json(
            $this->tripRepository->statistics()
        );
    }
}
