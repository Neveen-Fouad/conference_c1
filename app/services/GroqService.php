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

    public function MakeTrip($TripRequest, $weatherForecast, $hotel)
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
            // 'available_attractions' => $places
        ]);

        try {
            $response = Groq::chat()->completions()->create([
                "model" => config("groq.model"), 
                "messages" => [
                    [
                        "role" => "system",
                        "content" => "You are an expert travel planner. You must output your response in a good readable format with proper formatting and structure.
        Generate a daily itinerary for {$days} days to {$destination} with a budget of {$budget} and for {$guests} guests.
        
        CRITICAL RULES: 
        1. You MUST build the trip realistically and keep in mind the hotel information in the context data.
        2. Look at the weather forecast provided in the context data. Plan indoor activities on rainy days and outdoor activities on sunny days.
        3. Factor in the budget.

        Context Data:
        {$contextData}
        
        You must include these data:
        {
           trip: [
                {   best hotel for all days based on the hotel information in the context data and budget and the number of guests and days,
                    day: 1,
                    weather_note: Brief note on today's weather in the destination,
                    morning: Attraction Name and description,
                    lunch: Restaurant suggestion based on the Attraction address and budget,
                    afternoon: Attraction Name and description,
                    dinner: Restaurant suggestion based on the Attraction address and budget,
                    daily_cost: 0
                }
            ],
            total_estimated_cost: 0
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
        return $response['choices'][0]['message']['content'];
    }
}
