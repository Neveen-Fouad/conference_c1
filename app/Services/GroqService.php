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

    public function MakeTrip($TripRequest, $weatherForecast, $hotel, $places)
    {
       
    $StartDate = $TripRequest['start_date']?? $TripRequest->start_date;
        $EndDate = $TripRequest['end_date'] ?? $TripRequest->end_date;
        $days = (new \DateTime($EndDate))->diff(new \DateTime($StartDate))->days + 1;
        $destination = $TripRequest['destination'] ?? $TripRequest->destination;
        $budget = $TripRequest['budget'] ?? $TripRequest->budget;
        $guests = $TripRequest['number_of_travels'] ?? $TripRequest->number_of_travels;
        
        $contextData = json_encode([
            // 'city' => $city,
            'weather_forecast' => $weatherForecast,
            'hotel_info' => $hotel,
            'available_attractions' => $places
        ]);

        try {
            $response = Groq::chat()->completions()->create([
                "model" => config("groq.model"),
                "response_format" => ["type" => "json_object"], 
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are an expert travel planner. You must output your response in a good readable format with proper formatting and structure.
        Generate a daily itinerary for {$days} days to {$destination} with a budget of {$budget} and for {$guests} guests.
        
        CRITICAL RULES: 
        1. You MUST build the trip realistically and keep in mind the hotel information in the context data.
        2. Look at the weather forecast provided in the context data. Plan indoor activities on rainy days and outdoor activities on sunny days.
        3. Factor in the budget.
        4. suggest attractions based on the available attractions in the context data.

        Context Data:
        {$contextData}
       Respond with ONLY valid JSON (no markdown, no commentary) in exactly this shape:
{
  \"best_hotel\": \"name of best hotel for the whole stay, chosen from hotel_info based on budget and guest count\",
  \"trip\": [
    {
      \"day\": 1,
      \"weather_note\": \"brief note on today's weather\",
      \"morning\": \"attraction name and description\",
      \"lunch\": \"restaurant suggestion near the morning attraction, within budget\",
      \"afternoon\": \"attraction name and description\",
      \"dinner\": \"restaurant suggestion near the afternoon attraction, within budget\",
      \"route_notes\": \"how to get between hotel and attractions, based on available transportation; say if none available\",
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
        $content = $response['choices'][0]['message']['content'];

$decoded = json_decode($content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    Log::error('Groq returned invalid JSON', ['content' => $content, 'error' => json_last_error_msg()]);
    throw new \RuntimeException('AI returned an unexpected format. Please try again.');
}

return $decoded;
 
    }
}
