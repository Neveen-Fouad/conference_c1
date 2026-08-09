<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateclientInterestRequest;
use App\Models\Interests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class InterestsController extends Controller
{
   
    // public function index()
    // {
    //     $interests = Cache::remember('interests.all',now()->addHour(),function () { 
    //         return Interests::select('id', 'name')->get();
    //     });

    //     return response()->json($interests);
    // }

//     public function clientInterests(Request $request)
// {
//     //$client = $request->user()->client;
//       // Temporary for testing without auth
//      $client = \App\Models\Client::first();

//     if (!$client) {
//         return response()->json([
//             'message' => 'No client profile found.'
//         ], 404);
//     }

//     $interests = Cache::remember(
//         "client_{$client->id}_interests",
//         now()->addHour(),
//         function () use ($client) {
//             return $client->interests()
//                 ->select('interests.id', 'name')
//                 ->get();
//         }
//     );

//     return response()->json($interests);
// }
    // public function updateClientInterests(UpdateclientInterestRequest $request)
    // {
        
    //    // $client = $request->user()->client;
    //     $client = \App\Models\Client::first();

    //     if (!$client) {
    //         return response()->json([
    //             'message' => 'No client profile found.'
    //         ], 404);
    //     }
        
    // $client->interests()->syncWithoutDetaching($request->interests);

    //     Cache::forget("client_{$client->id}_interests");

    //     $interests = Cache::remember("client_{$client->id}_interests",now()->addHour(),function () use ($client) {return $client->interests()->select('interests.id', 'name')->get();});

    //     return response()->json([
    //         'message' => 'Interests updated successfully.',
    //         'interests' => $interests,
    //     ]);
    // }
}
