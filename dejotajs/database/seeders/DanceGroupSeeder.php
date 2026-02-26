<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DanceGroup;
use Faker\Factory as Faker;
class DanceGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('lv_LV');

        $groups = [
            // Lyrical dance
            ['name' => 'DancArt', 'genre' => 'lyrical dance'],
            ['name' => 'ArtDance Studio', 'genre' => 'lyrical dance'],

            // Ballet
            ['name' => 'Rīgas Baleta Skola', 'genre' => 'ballet'],
            ['name' => 'Latvijas Nacionālais Balets', 'genre' => 'ballet'],

            // Contemporary dance
            ['name' => 'Dzirnas', 'genre' => 'contemporary dance'],
            ['name' => 'Motion Dance Studio', 'genre' => 'contemporary dance'],
            ['name' => 'ConTempo', 'genre' => 'contemporary dance'],

            // Folk dance
            ['name' => 'Teiksma', 'genre' => 'folk dance'],
            ['name' => 'Līgo', 'genre' => 'folk dance'],
            ['name' => 'Auseklītis', 'genre' => 'folk dance'],

            // Hip hop
            ['name' => 'Ritms', 'genre' => 'hip hop'],
            ['name' => 'Street Crew Riga', 'genre' => 'hip hop'],
            ['name' => 'Beat Squad', 'genre' => 'hip hop'],

            // Other
            ['name' => 'Zelta Sietiņš', 'genre' => 'other'],
            ['name' => 'Pērle Dance', 'genre' => 'other'],
        ];

          foreach ($groups as $group) {
            DanceGroup::create([
                'name' => $group['name'],
                'description' => $faker->paragraph(),
                'city' => $faker->city,
                'address' => $faker->streetAddress,
                'picture' => 'https://picsum.photos/seed/' . urlencode($group['name']) . '/640/480',
                'approval' => 'https://www.example.com/docs/' . strtolower(str_replace(' ', '_', $group['name'])) . '_document.pdf',
                'status' => collect(['approved','waiting'])->random(),
                'genre' => $group['genre'],
            ]);
        }
    }
}
