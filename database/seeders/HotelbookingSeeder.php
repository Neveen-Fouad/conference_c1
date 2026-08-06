<?php

namespace Database\Seeders;

use App\Models\bookings;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class HotelbookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = Client::pluck('id');

        if ($clientIds->isEmpty()) {
            $this->command->warn('No clients found. Seed clients before running HotelBookingSeeder.');
            return;
        }

        $roomClasses = ['standard', 'deluxe', 'suite', 'executive'];
        $statuses = ['pending', 'confirmed', 'cancelled'];
        $providers = ['Hotels.com', 'Booking.com', 'Expedia'];

        for ($i = 0; $i < 20; $i++) {
            $checkInDate = Carbon::now()->addDays(rand(1, 90));
            $checkOutDate = (clone $checkInDate)->addDays(rand(1, 14));
            $roomClass = $roomClasses[array_rand($roomClasses)];
            $guests = rand(1, 4);

            bookings::create([
                'client_id' => $clientIds->random(),
                'type' => 'hotel',
                'provider' => $providers[array_rand($providers)],
                'external_reference_id' => (string) rand(100000, 999999),
                'number_of_days' => $checkInDate->diffInDays($checkOutDate),
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'booking_date' => Carbon::now(),
                'number_of_bookings' => $guests,
                'classes' => $roomClass,
                'status' => $statuses[array_rand($statuses)],
                'total_price' => rand(100, 3000) + (rand(0, 99) / 100),
                'currency' => 'USD',
                'details' => [
                    'hotel_id' => rand(1000, 9999),
                    'check_in_date' => $checkInDate->toDateString(),
                    'check_out_date' => $checkOutDate->toDateString(),
                    'guests' => $guests,
                    'room_class' => $roomClass,
                ],
            ]);
        }

        $this->command->info('Seeded 20 hotel bookings.');
    }
}