<?php

namespace App\Services;

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

    public function MakeTrip($TripRequest, $weatherForecast, $hotel, $places, $restaurants = [], $recommendedHotel = null)
    {
        $days = $TripRequest['number_of_days'] ?? $TripRequest->number_of_days;
        $destination = $TripRequest['destination'] ?? $TripRequest->destination;
        $budget = $TripRequest['budget'] ?? $TripRequest->budget;
        $guests = $TripRequest['number_of_travels'] ?? $TripRequest->number_of_travels;
        $preferences = $TripRequest['preferences'] ?? $TripRequest->preferences ?? null;

        $contextData = json_encode([
            'weather_forecast' => $weatherForecast,
            'hotel_info' => $hotel,
            'recommended_hotel' => $recommendedHotel,
            'available_attractions' => $places,
            'available_restaurants' => $restaurants,
        ]);

        $recommendedHotelNote = $recommendedHotel
            ? "The 'travel_time_from_hotel' figures in available_attractions and available_restaurants were calculated using \"{$recommendedHotel}\" as the point of origin. You SHOULD set 'best_hotel' to \"{$recommendedHotel}\" unless it is clearly unsuitable for the budget or guest count — if you pick a different hotel from hotel_info, say in 'route_notes' that travel times are approximate."
            : "No recommended hotel was pre-selected; choose the best fit from hotel_info based on budget and guest count.";
            
        $preferencesNote = $preferences 
            ? "\n        9. USER PREFERENCES: The user has specified the following additional preferences for the trip: \"{$preferences}\". You MUST accommodate these preferences in your itinerary choices (such as attractions, restaurants, or specific exclusions)." 
            : "";

        try {
            $response = Groq::chat()->completions()->create([
                "model" => config("groq.model"),
                "response_format" => ["type" => "json_object"],
                "messages" => [
                    [
                        'role' => 'system',
                        'content' => "You are an expert travel planner. You must output your response in a good readable format with proper formatting and structure.
        Generate a daily itinerary for {$days} days to {$destination} with a budget of {$budget} and for {$guests} guests.

        CRITICAL RULES:
        1. You MUST build the trip realistically and keep in mind the hotel information in the context data. {$recommendedHotelNote}
        2. Look at the 'weather_forecast' provided in the context data. For each day, if an exact temperature is provided in 'daily_avg_temp_c', you MUST output that precise number for 'weather_temperature'. If some or all days are missing from the forecast, realistically estimate the temperature based on the destination's natural climate for that month.
        3. The target budget of {$budget} is in the LOCAL currency of the destination '{$destination}'. For each day, estimate:
           - 'hotel_per_night': the shared per-night room cost (NOT per guest — do not multiply this by {$guests}).
           - 'activities_and_meals_cost_per_person': the cost for meals and activities for ONE person for that day, in local currency. This MUST be a per-person figure — do NOT multiply it by {$guests} yourself, the guest multiplication is handled outside of your response.
           If a place says \"Price not available\" or uses symbols like \"$$\", make a realistic numeric per-person estimate in the local currency. NEVER set costs to 0.
        4. Suggest attractions based on the available attractions in the context data.
        5. Suggest lunch and dinner options based on the available_restaurants in the context data.
        6. You MUST explicitly use the 'travel_time_from_hotel' data provided in both the available_attractions and available_restaurants context when generating the route_notes for each day.
        7. You MUST NOT suggest any attractions, restaurants, or hotels that are NOT present in the context data (available_attractions, available_restaurants, hotel_info).
       

        Context Data:
        {$contextData}
       Respond with ONLY valid JSON (no markdown, no commentary) in exactly this shape. The morning, lunch, afternoon, and dinner fields MUST be strings, NOT nested objects. Costs are estimates only — the caller will independently compute final totals, so accuracy matters more than the math working out perfectly here:
{
  \"best_hotel\": \"name of best hotel for the whole stay, chosen from hotel_info based on budget and guest count\",
  \"trip\": [
    {
      \"day\": 1,
      \"day_title\": \"a catchy and descriptive title for this day's itinerary\",
      \"weather_note\": \"brief note on today's weather based on logical climate estimation\",
      \"weather_temperature\": \"estimated average degrees (C or F)\",
      \"hotel_name\": \"name of the hotel\",
      \"hotel_per_night\": 0,
      \"morning\": \"attraction name and description\",
      \"lunch\": \"restaurant suggestion near the morning attraction, within budget\",
      \"afternoon\": \"attraction name and description\",
      \"dinner\": \"restaurant suggestion near the afternoon attraction, within budget\",
      \"route_notes\": \"how to get between hotel and attractions, based on available transportation; say if none available\",
      \"activities_and_meals_cost_per_person\": 0
    }
  ]
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
            Log::error('groq error ', ['message' => $e->getMessage()]);
            throw new \RuntimeException('Failed to generate trip plan. Please try again later.');
        }

        $content = data_get($response, 'choices.0.message.content');

        if ($content === null) {
            Log::error('Groq response missing expected content field', ['response' => $response]);
            throw new \RuntimeException('AI returned an unexpected response format. Please try again.');
        }

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Groq returned invalid JSON', ['content' => $content, 'error' => json_last_error_msg()]);
            throw new \RuntimeException('AI returned an unexpected format. Please try again.');
        }

        return $decoded;
    }
}