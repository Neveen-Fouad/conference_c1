<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Interfaces\TripRepositoryInterface;
use App\Models\Client;
use App\Models\Trip;
use Illuminate\Support\Carbon;
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
        $trips = $user->role === 'admin'
            ? $this->tripRepository->getAll()
            : $this->tripRepository->findByUserId($user->id);

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
        $validated['end_date'] = $this->calculateEndDate($validated);

        $trip = DB::transaction(function () use ($validated) {
            $tripRecord = $this->tripRepository->create(
                collect($validated)->except('details')->toArray()
            );

            foreach ($validated['details'] ?? [] as $detail) {
                $tripRecord->details()->create([
                    'day' => $detail['day'],
                    'title' => $detail['title'],
                    'expenses' => $detail['expenses'] ?? 0,
                    'plan' => is_array($detail['plan'])
                        ? json_encode($detail['plan'])
                        : $detail['plan'],
                ]);
            }

            return $tripRecord->load('details');
        });

        return response()->json($trip, 201);
    }

    public function update(UpdateTripRequest $request, $id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('update', $trip);

        $data = $request->validated();
        $data['end_date'] = $this->calculateEndDate($data);

        return response()->json($this->tripRepository->update($id, $data));
    }

    public function destroy($id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('delete', $trip);

        return response()->json($this->tripRepository->delete($id));
    }

    public function getTripsByUserId($userId)
    {
        $user = Auth::user();
        if ($user->role !== 'admin' && $user->id != $userId) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($this->tripRepository->findByUserId($userId));
    }

    public function getTripDays($id)
    {
        $trip = $this->tripRepository->findById($id);
        Gate::authorize('view', $trip);

        return response()->json($this->tripRepository->getTripDetails($id));
    }

    public function statistics()
    {
        return response()->json($this->tripRepository->statistics());
    }

    public function getPreMadeTrips()
    {
        return response()->json($this->tripRepository->getPreMadeTrips());
    }

    public function book($id)
    {
        $user = Auth::user();
        $client = Client::where('user_id', $user->id)->first();

        if (! $client) {
            return response()->json(['message' => 'Client profile not found for this user.'], 403);
        }

        try {
            $booking = $this->tripRepository->bookTrip($id, $client->id);

            return response()->json([
                'message' => 'Trip booked successfully.',
                'data' => $booking,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error booking trip.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function calculateEndDate(array $data): string
    {
        return Carbon::parse($data['start_date'])
            ->addDays($data['number_of_days'])
            ->toDateString();
    }
}
