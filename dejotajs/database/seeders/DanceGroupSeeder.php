<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanceGroup;

class DanceGroupSeeder extends Seeder
{
    public function run(): void
    {

        DanceGroup::factory()
            ->count(30)
            ->create([
                'status' => 'approved'
            ]);

        DanceGroup::factory()
            ->count(20)
            ->create([
                'status' => fake()->randomElement(['waiting', 'declined'])
            ]);
    }
}
