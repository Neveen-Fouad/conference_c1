<?php

namespace App\services;

use Log;
use LucianoTonet\GroqLaravel\Facades\Groq;
use Throwable;

class GroqService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    function MakeTrip($TripRequest){
    try {
        $response = Groq::chat()->completions()->create([
            "model"=> config(" groq.model"),
            "messages"=> [
                [
                    "role"=> "system",
                    "content"=> "You are a helpful assistant that creates a daily trip plan based on the user's input and generate an estimated expenses based on the provided budget information."
                ],
                [
                    "role"=> "user",
                    "content"=> $TripRequest->destination . " " . $TripRequest->start_date . " " . $TripRequest->number_of_days . " "  ." ". $TripRequest->budget
                ]
            ], "temperature"=> 1.5
            
        ]);
    }catch(Throwable $e){
        Log::error('groq error ' , ["message" => $e->getMessage()]);
        throw new \RuntimeException('Failed to generate trip plan. Please try again later.');
    }
    return $response->choices[0]->message->content?? 'error';
    }
    
}
