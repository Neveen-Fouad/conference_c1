<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateclientInterestRequest;
use App\Models\Interest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InterestsController extends Controller
{
    public function index()
    {
        $interests = Cache::remember('interests.all', now()->addHour(), function () {
            return Interest::select('id', 'name')->get();
        });

        return response()->json($interests);
    }

    public function clientInterests(Request $request)
    {
        $client = $request->user()->client;

        if (! $client) {
            return response()->json(['message' => 'No client profile found.'], 404);
        }

        $interests = $client->interests()->select('interests.id', 'interests.name')->get();

        return response()->json($interests);
    }

    public function updateClientInterests(UpdateclientInterestRequest $request)
    {
        $client = $request->user()->client;

        if (! $client) {
            return response()->json(['message' => 'No client profile found.'], 404);
        }

        $client->interests()->sync($request->interests);

        $interests = $client->interests()->select('interests.id', 'interests.name')->get();

        return response()->json([
            'message' => 'Interests updated successfully.',
            'interests' => $interests,
        ]);
    }
}
