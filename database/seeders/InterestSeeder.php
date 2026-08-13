<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Interest;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
   
    public function run(): void
    {
        
        $interests = [
            'Adventure',
            'Beach',
            'Nature',
            'History',
            'Culture',
            'Food',
            'Shopping',
            'Nightlife',
            'Luxury',
            'Family',
            'Photography',
            'Relaxation',
       ];
     foreach ($interests as $interest) {
    Interest::updateOrCreate([
        'name' => $interest,
    ]);
}

    }
}
