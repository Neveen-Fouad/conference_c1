<?php

namespace Database\Seeders;

use App\Models\bookings;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FlightbookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = Client::pluck('id');

        if ($clientIds->isEmpty()) {
            $this->command->warn('No clients found. Seed clients before running FlightBookingSeeder.');
            return;
        }

        $cabinClasses = ['economy', 'premium_economy', 'business', 'first'];
        $statuses = ['pending', 'confirmed', 'cancelled'];
        $providers = ['Amadeus', 'Skyscanner', 'Sabre'];

        for ($i = 0; $i < 20; $i++) {
            $departureDate = Carbon::now()->addDays(rand(1, 90));
            $returnDate = (clone $departureDate)->addDays(rand(1, 14));
            $cabinClass = $cabinClasses[array_rand($cabinClasses)];
            $passengers = rand(1, 4);

            bookings::create([
                'client_id' => $clientIds->random(),
                'type' => 'flight',
                'provider' => $providers[array_rand($providers)],
                'external_reference_id' => (string) rand(100000, 999999),
                'number_of_days' => $departureDate->diffInDays($returnDate),
                'check_in_date' => $departureDate,
                'check_out_date' => $returnDate,
                'booking_date' => Carbon::now(),
                'number_of_bookings' => $passengers,
                'classes' => $cabinClass,
                'status' => $statuses[array_rand($statuses)],
                'total_price' => rand(100, 3000) + (rand(0, 99) / 100),
                'currency' => 'USD',
                'details' => [
                    'flight_id' => rand(1000, 9999),
                    'departure_date' => $departureDate->toDateString(),
                    'return_date' => $returnDate->toDateString(),
                    'passengers' => $passengers,
                    'cabin_class' => $cabinClass,
                ],
            ]);
        }

        $this->command->info('Seeded 20 flight bookings.');
    }
}