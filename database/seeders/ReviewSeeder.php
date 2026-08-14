<?php

namespace Database\Seeders;

use App\Enum\ReviewStatus;
use App\Enum\ReviewType;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Review;
use App\Models\Trip;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $clientIds = Client::pluck('id');

        if ($clientIds->isEmpty()) {
            $this->command->warn('No clients found. Seed clients before running ReviewSeeder.');

            return;
        }

        $tripDescriptions = [
            'The itinerary was well paced and left enough time to explore independently.',
            'Everything was clearly organized, from the recommendations to the daily schedule.',
            'A memorable trip with thoughtful activities and a smooth overall experience.',
            'The destination suggestions were excellent and matched the travel style very well.',
            'Great planning overall; a little more flexibility in the schedule would make it even better.',
        ];
        $bookingDescriptions = [
            'The booking process was straightforward and the experience met my expectations.',
            'The details matched the booking and the service was smooth from start to finish.',
            'A reliable option overall, with clear information and no unexpected issues.',
            'Good value for the price. I would consider booking this again.',
            'The experience was acceptable, though communication could have been clearer.',
        ];
        $statuses = [ReviewStatus::Approved, ReviewStatus::pending, ReviewStatus::Rejected];
        $created = 0;

        $trips = Trip::query()->select('id')->get();

        if ($trips->isEmpty()) {
            $this->command->warn('No trips found. Skipping trip reviews.');
        }

        foreach ($trips as $index => $trip) {
            $clientId = $clientIds->random();

            if (Review::query()
                ->where('client_id', $clientId)
                ->where('type', ReviewType::Trip->value)
                ->where('reviewable_id', (string) $trip->id)
                ->exists()) {
                continue;
            }

            Review::create([
                'client_id' => $clientId,
                'reviewable_id' => (string) $trip->id,
                'type' => ReviewType::Trip,
                'rating' => number_format(random_int(10, 50) / 10, 1, '.', ''),
                'description' => $tripDescriptions[array_rand($tripDescriptions)],
                'status' => $statuses[$index % count($statuses)],
                'image' => null,
            ]);

            $created++;
        }

        $bookings = Booking::query()
            ->select('id', 'client_id', 'type')
            ->whereNotNull('client_id')
            ->get();

        if ($bookings->isEmpty()) {
            $this->command->warn('No bookings found. Skipping booking reviews.');
        }

        foreach ($bookings as $index => $booking) {
            if (Review::query()->where('booking_id', $booking->id)->exists()) {
                continue;
            }

            Review::create([
                'client_id' => $booking->client_id,
                'booking_id' => $booking->id,
                'reviewable_id' => (string) $booking->id,
                'type' => ReviewType::from($booking->type),
                'rating' => number_format(random_int(10, 50) / 10, 1, '.', ''),
                'description' => $bookingDescriptions[array_rand($bookingDescriptions)],
                'status' => $statuses[$index % count($statuses)],
                'image' => null,
            ]);

            $created++;
        }

        $this->command->info("Seeded {$created} reviews.");
    }
}
