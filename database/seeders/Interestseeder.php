<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Interests;
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
    Interests::updateOrCreate([
        'name' => $interest,
    ]);
}

    }
}
