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

    public function MakeTrip($TripRequest, $weatherForecast, $hotel, $places, $restaurants = [])
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
            'available_attractions' => $places,
            'available_restaurants' => $restaurants
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
        2. Look at the 'weather_forecast' provided in the context data. For each day, if an exact temperature is provided in 'daily_avg_temp_c', you MUST output that precise number for 'weather_temperature'. If some or all days are missing from the forecast, realistically estimate the temperature based on the destination's natural climate for that month.
        3. Factor in the budget. The target budget of {$budget} is in the LOCAL currency of the destination '{$destination}'. You MUST ensure the 'total_estimated_cost' does NOT exceed this budget. Calculate the 'daily_cost' and 'total_estimated_cost' by adding up the 'fees' and 'price' fields in the context data. IMPORTANT: If a place says \"Price not available\" or uses symbols like \"$$\", you MUST make a realistic numeric estimate IN THE LOCAL CURRENCY of the destination. NEVER set costs to 0. Multiply the cost of all activities and meals by {$guests}. The 'hotel_per_night' cost is shared per room, do NOT multiply it by {$guests}.
        4. suggest attractions based on the available attractions in the context data.
        5. suggest lunch and dinner options based on the available_restaurants in the context data.
        6. You MUST explicitly use the 'travel_time_from_hotel' data provided in both the available_attractions and available_restaurants context when generating the route_notes for each day.

        Context Data:
        {$contextData}
       Respond with ONLY valid JSON (no markdown, no commentary) in exactly this shape. The morning, lunch, afternoon, and dinner fields MUST be strings, NOT nested objects:
{
  \"best_hotel\": \"name of best hotel for the whole stay, chosen from hotel_info based on budget and guest count\",
  \"trip\": [
    {
      \"day\": 1,
      \"weather_note\": \"brief note on today's weather based on logical climate estimation\",
      \"weather_temperature\": \"estimated average degrees (C or F)\",
      \"hotel_name\": \"name of the hotel\",
      \"hotel_per_night\": 0,
      \"morning\": \"attraction name and description\",
      \"lunch\": \"restaurant suggestion near the morning attraction, within budget\",
      \"afternoon\": \"attraction name and description\",
      \"dinner\": \"restaurant suggestion near the afternoon attraction, within budget\",
      \"route_notes\": \"how to get between hotel and attractions, based on available transportation; say if none available\",
      \"activities_and_meals_cost_per_person\": 0,
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
