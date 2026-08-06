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

    public function MakeTrip($TripRequest, $city, $weatherForecast, $places)
    {
       
        $days = $TripRequest['number_of_days'] ?? $TripRequest->number_of_days;
        $destination = $TripRequest['destination'] ?? $TripRequest->destination;
        $budget = $TripRequest['budget'] ?? $TripRequest->budget;
        
        $contextData = json_encode([
            'city' => $city,
            'weather_forecast' => $weatherForecast,
            'available_attractions' => $places
        ]);

        try {
            $response = Groq::chat()->completions()->create([
                "model" => config("groq.model"), 
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are an expert travel planner. You must output your response strictly in JSON format. 
        Generate a daily itinerary for {$days} days to {$destination} with a budget of {$budget}.
        
        CRITICAL RULES: 
        1. You MUST build the trip using ONLY the attractions provided in the 'Context Data' below. Do not invent places.
        2. Look at the weather forecast in the Context Data. Plan indoor activities on rainy days and outdoor activities on sunny days.
        3. Factor in the budget.

        Context Data:
        {$contextData}
        
        You must use this exact JSON schema:
        {
            \"trip\": [
                {
                    \"day\": 1,
                    \"weather_note\": \"Brief note on today's weather\",
                    \"morning\": \"Attraction Name and description\",
                    \"lunch\": \"Restaurant suggestion\",
                    \"afternoon\": \"Attraction Name and description\",
                    \"dinner\": \"Restaurant suggestion\",
                    \"daily_cost\": 0
                }
            ],
            \"total_estimated_cost\": 0
        }"
                    ],
                    [
                        "role" => "user",
                        "content" => "Plan my trip." 
                    ]
                ], 
                "temperature" => 0.8, 
            ]);
            
        } catch (Throwable $e) {
            Log::error('groq error ', ["message" => $e->getMessage()]);
            throw new \RuntimeException('Failed to generate trip plan. Please try again later.');
        }
        
        return $response->choices[0]->message->content ?? 'error';
    }
}
