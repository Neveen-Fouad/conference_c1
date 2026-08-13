<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\FlightBookingSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    public function run(): void
    {$this->call([
    AdminSeeder::class,
    InterestSeeder::class,
    HotelBookingSeeder::class,
    FlightBookingSeeder::class,
    DefaultTripSeeder::class,
]);
    }
}
