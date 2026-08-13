<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Interfaces\TripRepositoryInterface;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TripController extends Controller
{
    protected $tripRepository;

    public function __construct(TripRepositoryInterface $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }

    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $trips = $this->tripRepository->getAll();
        } else {
            $trips = $this->tripRepository->findByUserId($user->id);
        }

        return response()->json($trips);
    }

    public function show($id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('view', $trip);

        return response()->json($trip);
    }

    public function store(StoreTripRequest $request)
    {
        Gate::authorize('create', Trip::class);
        $validated = $request->validated();
        $validated['classes'] ??= 'economy';

        $trip = DB::transaction(function () use ($validated) {

            $tripRecord = $this->tripRepository->create(collect($validated)->except('details')->toArray());

            if (isset($validated['details']) && is_array($validated['details'])) {
                foreach ($validated['details'] as $detail) {
                    $tripRecord->details()->create([
                        'day' => $detail['day'],
                        'title' => $detail['title'],
                        'expenses' => $detail['expenses'] ?? 0,
                        'plan' => is_array($detail['plan']) ? json_encode($detail['plan']) : $detail['plan'],
                    ]);
                }
            }

            return $tripRecord->load('details');
        });

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

    public function destroy($id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('delete', $trip);

        return response()->json(
            $this->tripRepository->delete($id)
        );
    }

    public function getTripsByUserId($userId)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id != $userId) {
            abort(403, 'Unauthorized action.');
        }

        $trips = $this->tripRepository->findByUserId($userId);

        return response()->json($trips);
    }

    public function getTripDays($id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('view', $trip);

        $details = $this->tripRepository->getTripDetails($id);

        return response()->json($details);
    }

    public function statistics()
    {
        return response()->json(
            $this->tripRepository->statistics()
        );
    }

    public function getPreMadeTrips()
    {
        $trips = $this->tripRepository->getPreMadeTrips();

        return response()->json($trips);
    }
}
