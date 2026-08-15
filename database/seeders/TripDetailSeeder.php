<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\TripDetail;
use Illuminate\Database\Seeder;

class TripDetailSeeder extends Seeder
{
    public function run(): void
    {
        $templates = $this->templates();

        Trip::query()->get()->each(function (Trip $trip) use ($templates) {
            $template = $templates[$trip->destination] ?? null;

            if (! $template || $trip->is_ai_generated) {
                return;
            }

            if ((int) $trip->number_of_days !== count($template['days'])) {
                return;
            }

            TripDetail::where('trip_id', $trip->id)->delete();

            $perDay = round(((float) $trip->estimated_expenses ?: 500.00) / max(1, $trip->number_of_days), 2);

            foreach ($template['days'] as $index => $day) {
                TripDetail::create([
                    'trip_id' => $trip->id,
                    'day' => $index + 1,
                    'title' => $day['title'],
                    'expenses' => $day['cost'] ?? $perDay,
                    'plan' => json_encode([
                        'summary' => $day['summary'] ?? null,
                        'morning' => $day['morning'] ?? null,
                        'lunch' => $day['lunch'] ?? null,
                        'afternoon' => $day['afternoon'] ?? null,
                        'evening' => $day['evening'] ?? null,
                        'night' => $day['night'] ?? null,
                        'activities' => $day['activities'] ?? null,
                        'daily_cost' => $day['cost'] ?? $perDay,
                        'hotel' => $template['hotel'],
                        'hotel_per_night' => $template['hotel_per_night'],
                        'weather_note' => $day['weather_note'] ?? null,
                    ]),
                ]);
            }
        });
    }

    private function templates(): array
    {
        return [
            'Cairo, Egypt' => [
                'hotel' => 'Cleopatra Luxury Hotel Cairo',
                'hotel_per_night' => 120.00,
                'days' => [
                    [
                        'title' => 'Arrival & Khan el-Khalili',
                        'morning' => ['Arrival and hotel check-in', 'Relax after the flight'],
                        'afternoon' => ['Khan el-Khalili bazaar', 'Walk through Al-Azhar Park'],
                        'evening' => ['Nile dinner cruise'],
                        'night' => ['Evening stroll along the Corniche'],
                        'weather_note' => 'Mild October weather, light jacket in the evening',
                    ],
                    [
                        'title' => 'Pyramids & the Sphinx',
                        'morning' => ['Giza Pyramids', 'The Great Sphinx'],
                        'lunch' => ['Lunch near the pyramids viewpoint'],
                        'afternoon' => ['Solar Boat Museum', 'Panoramic camel ride viewpoint'],
                        'evening' => ['Pyramids Sound & Light show'],
                    ],
                    [
                        'title' => 'Egyptian Museum & Downtown',
                        'morning' => ['Egyptian Museum', 'Tutankhamun galleries'],
                        'afternoon' => ['Tahrir Square', 'Downtown Cairo architecture'],
                        'evening' => ['Dinner in Old Cairo'],
                    ],
                    [
                        'title' => 'Islamic Cairo',
                        'morning' => ['Citadel of Saladin', 'Mosque of Muhammad Ali'],
                        'afternoon' => ['Sultan Hassan Mosque', 'More of Khan el-Khalili'],
                        'evening' => ['Local dinner in Old Cairo'],
                    ],
                    [
                        'title' => 'Nile Felucca & Departure',
                        'morning' => ['Felucca ride on the Nile'],
                        'afternoon' => ['Souvenir shopping', 'Packing and departure'],
                    ],
                ],
            ],
            'Tokyo, Japan' => [
                'hotel' => 'Hotel Gracery Shinjuku',
                'hotel_per_night' => 180.00,
                'days' => [
                    [
                        'title' => 'Arrival & Shinjuku',
                        'morning' => ['Arrival at Narita', 'Check-in and settle in'],
                        'afternoon' => ['Shinjuku exploration', 'Godzilla head and Omoide Yokocho'],
                        'evening' => ['Neon lights of Kabukicho', 'Golden Gai bars'],
                    ],
                    [
                        'title' => 'Imperial Palace & Ginza',
                        'morning' => ['Imperial Palace East Gardens'],
                        'afternoon' => ['Ginza shopping streets'],
                        'evening' => ['Kaiseki dinner in Ginza'],
                    ],
                    [
                        'title' => 'Asakusa & Senso-ji',
                        'morning' => ['Senso-ji Temple', 'Nakamise Street'],
                        'afternoon' => ['Sumida River cruise'],
                        'evening' => ['Akihabara electronics town'],
                    ],
                    [
                        'title' => 'Shibuya & Harajuku',
                        'morning' => ['Meiji Shrine'],
                        'afternoon' => ['Harajuku Takeshita Street', 'Owl cafes and crepes'],
                        'evening' => ['Shibuya Crossing', 'Skyline views from Shibuya Sky'],
                    ],
                    [
                        'title' => 'Mt. Fuji Day Trip',
                        'morning' => ['Coach to Mt. Fuji area'],
                        'afternoon' => ['Hakone Ropeway', 'Lake Ashi cruise'],
                        'evening' => ['Return to Tokyo'],
                    ],
                    [
                        'title' => 'Ueno & TeamLab',
                        'morning' => ['Ueno Park', 'Tokyo National Museum'],
                        'afternoon' => ['teamLab Planets immersive art'],
                        'evening' => ['Dinner at Tsukiji Outer Market'],
                    ],
                    [
                        'title' => 'Free Day & Departure',
                        'morning' => ['Leisure morning', 'Last-minute sightseeing'],
                        'afternoon' => ['Final souvenir shopping', 'Departure'],
                    ],
                ],
            ],
            'Paris, France' => [
                'hotel' => 'Hotel des Arts Montmartre',
                'hotel_per_night' => 210.00,
                'days' => [
                    [
                        'title' => 'Arrival & Eiffel Tower',
                        'morning' => ['Arrival and check-in'],
                        'afternoon' => ['Champ de Mars', 'Eiffel Tower summit'],
                        'evening' => ['Seine river cruise'],
                    ],
                    [
                        'title' => 'Louvre & Tuileries',
                        'morning' => ['Louvre Museum highlights'],
                        'afternoon' => ['Tuileries Gardens', 'Place de la Concorde'],
                        'evening' => ['Dinner in Le Marais'],
                    ],
                    [
                        'title' => 'Montmartre & Sacré-Cœur',
                        'morning' => ['Sacré-Cœur Basilica'],
                        'afternoon' => ['Montmartre artists square', 'Wall of Love'],
                        'evening' => ['Pigalle and Moulin Rouge area'],
                    ],
                    [
                        'title' => 'Notre-Dame & Latin Quarter',
                        'morning' => ['Île de la Cité', 'Notre-Dame exterior'],
                        'afternoon' => ['Latin Quarter', 'Panthéon'],
                        'evening' => ['Saint-Germain-des-Prés'],
                    ],
                    [
                        'title' => 'Versailles & Departure',
                        'morning' => ['Palace of Versailles'],
                        'afternoon' => ['Versailles gardens', 'Departure'],
                    ],
                ],
            ],
            'Rome, Italy' => [
                'hotel' => 'Hotel Colosseum',
                'hotel_per_night' => 165.00,
                'days' => [
                    [
                        'title' => 'Arrival & the Colosseum',
                        'morning' => ['Arrival and check-in'],
                        'afternoon' => ['Colosseum', 'Roman Forum'],
                        'evening' => ['Dinner in Trastevere'],
                    ],
                    [
                        'title' => 'Vatican City',
                        'morning' => ['St. Peter’s Basilica'],
                        'afternoon' => ['Vatican Museums', 'Sistine Chapel'],
                        'evening' => ['Stroll along the Tiber'],
                    ],
                    [
                        'title' => 'Pantheon & Piazza Navona',
                        'morning' => ['Pantheon'],
                        'afternoon' => ['Piazza Navona', 'Trevi Fountain'],
                        'evening' => ['Gelato and Spanish Steps'],
                    ],
                    [
                        'title' => 'Borghese Gardens & Appian Way',
                        'morning' => ['Borghese Gardens', 'Galleria Borghese'],
                        'afternoon' => ['Via Appia Antica bike ride'],
                        'evening' => ['Aperitivo in a piazza'],
                    ],
                    [
                        'title' => 'Castel Sant’Angelo & Departure',
                        'morning' => ['Castel Sant’Angelo'],
                        'afternoon' => ['Shopping near Piazza di Spagna', 'Departure'],
                    ],
                ],
            ],
            'Istanbul, Turkey' => [
                'hotel' => 'Tomtom Suites',
                'hotel_per_night' => 140.00,
                'days' => [
                    [
                        'title' => 'Arrival & Galata',
                        'morning' => ['Arrival and check-in'],
                        'afternoon' => ['Galata Tower', 'Istiklal Street'],
                        'evening' => ['Dinner in Beyoğlu'],
                    ],
                    [
                        'title' => 'Sultanahmet Highlights',
                        'morning' => ['Hagia Sophia'],
                        'afternoon' => ['Blue Mosque', 'Hippodrome'],
                        'evening' => ['Grand Bazaar glimpse'],
                    ],
                    [
                        'title' => 'Topkapi & the Cistern',
                        'morning' => ['Topkapi Palace'],
                        'afternoon' => ['Basilica Cistern', 'Spice Bazaar'],
                        'evening' => ['Bosphorus dinner cruise'],
                    ],
                    [
                        'title' => 'Bosphorus & Kadıköy',
                        'morning' => ['Bosphorus boat tour'],
                        'afternoon' => ['Kadıköy market', 'Asian side stroll'],
                        'evening' => ['Return to European side'],
                    ],
                    [
                        'title' => 'Dolmabahçe & Departure',
                        'morning' => ['Dolmabahçe Palace'],
                        'afternoon' => ['Grand Bazaar souvenir shopping', 'Departure'],
                    ],
                ],
            ],
            'Dubai, UAE' => [
                'hotel' => 'Rove Downtown Dubai',
                'hotel_per_night' => 160.00,
                'days' => [
                    [
                        'title' => 'Arrival & Downtown Dubai',
                        'morning' => ['Arrival and check-in'],
                        'afternoon' => ['Dubai Mall', 'Aquarium tunnel'],
                        'evening' => ['Burj Khalifa sunset', 'Fountain show'],
                    ],
                    [
                        'title' => 'Old Dubai & the Creek',
                        'morning' => ['Al Fahidi Historical District'],
                        'afternoon' => ['Abra ride across the Creek', 'Gold and Spice Souks'],
                        'evening' => ['Dinner in Al Seef'],
                    ],
                    [
                        'title' => 'Desert Safari',
                        'afternoon' => ['Dune bashing in the desert'],
                        'evening' => ['Desert camp BBQ and shows'],
                    ],
                    [
                        'title' => 'Palm & Marina',
                        'morning' => ['Palm Jumeirah', 'The Pointe and Atlantis'],
                        'afternoon' => ['Dubai Marina walk', 'Yacht views'],
                        'evening' => ['JBR beachfront dinner'],
                    ],
                    [
                        'title' => 'Modern Icons & Departure',
                        'morning' => ['Museum of the Future'],
                        'afternoon' => ['Mall of the Emirates', 'Departure'],
                    ],
                ],
            ],
            'Barcelona, Spain' => [
                'hotel' => 'Hotel Barcelona 1882',
                'hotel_per_night' => 150.00,
                'days' => [
                    [
                        'title' => 'Arrival & La Rambla',
                        'morning' => ['Arrival and check-in'],
                        'afternoon' => ['La Rambla', 'La Boqueria market'],
                        'evening' => ['Tapas in the Gothic Quarter'],
                    ],
                    [
                        'title' => 'Sagrada Família & Eixample',
                        'morning' => ['Sagrada Família'],
                        'afternoon' => ['Casa Batlló', 'Passeig de Gràcia'],
                        'evening' => ['Dinner in Gràcia'],
                    ],
                    [
                        'title' => 'Park Güell & Montjuïc',
                        'morning' => ['Park Güell'],
                        'afternoon' => ['Montjuïc hill and cable car'],
                        'evening' => ['Magic Fountain show'],
                    ],
                    [
                        'title' => 'Gothic Quarter & Barceloneta',
                        'morning' => ['Gothic Quarter walking tour'],
                        'afternoon' => ['Barceloneta beach'],
                        'evening' => ['Seafood paella by the sea'],
                    ],
                    [
                        'title' => 'Day Trip to Montserrat',
                        'morning' => ['Montserrat monastery'],
                        'afternoon' => ['Cable car and hikes'],
                        'evening' => ['Return to Barcelona'],
                    ],
                    [
                        'title' => 'Markets & Departure',
                        'morning' => ['Mercat de Sant Antoni', 'Souvenirs'],
                        'afternoon' => ['Final beach stroll', 'Departure'],
                    ],
                ],
            ],
            'Athens, Greece' => [
                'hotel' => 'Athens Center Square',
                'hotel_per_night' => 130.00,
                'days' => [
                    [
                        'title' => 'Arrival & Plaka',
                        'morning' => ['Arrival and check-in'],
                        'afternoon' => ['Plaka streets'],
                        'evening' => ['Rooftop dinner with Acropolis view'],
                    ],
                    [
                        'title' => 'Acropolis & Museum',
                        'morning' => ['Acropolis', 'Parthenon'],
                        'afternoon' => ['Acropolis Museum'],
                        'evening' => ['Monastiraki square'],
                    ],
                    [
                        'title' => 'Ancient Agora & Temple of Zeus',
                        'morning' => ['Ancient Agora'],
                        'afternoon' => ['Temple of Olympian Zeus', 'National Garden'],
                        'evening' => ['Psiri nightlife'],
                    ],
                    [
                        'title' => 'Cape Sounion Day Trip',
                        'morning' => ['Drive along the coast'],
                        'afternoon' => ['Temple of Poseidon'],
                        'evening' => ['Sunset drive back to Athens'],
                    ],
                    [
                        'title' => 'Syntagma & Departure',
                        'morning' => ['Syntagma Square', 'Changing of the Guard'],
                        'afternoon' => ['Souvenir shopping', 'Departure'],
                    ],
                ],
            ],
        ];
    }
}
