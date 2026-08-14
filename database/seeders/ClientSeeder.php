<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('name', 'User')->first();

        if (!$user) {
            $this->command->warn('User not found. Run UserSeeder first.');

            return;
        }

        Client::create([
            'user_id' => $user->id,
            'birth_date' => '2000-01-01',
            'phone' => '01000000000',
        ]);

        $this->command->info('Seeded client successfully.');
    }
}