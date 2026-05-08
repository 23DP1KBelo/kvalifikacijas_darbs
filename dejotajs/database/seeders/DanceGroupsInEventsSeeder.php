<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Event;
use App\Models\AgeGroup;
use Illuminate\Support\Facades\DB;

class DanceGroupsInEventsSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();
        $ageGroups = AgeGroup::all();

        if ($events->isEmpty() || $ageGroups->isEmpty()) {
            return;
        }

        foreach ($events as $event) {

            $count = rand(1, min(5, $ageGroups->count()));

            $selectedAgeGroups = $ageGroups->random($count);

            foreach ($selectedAgeGroups as $ageGroup) {

                DB::table('dance_groups_in_events')->insert([
                    'event_id' => $event->id,
                    'age_group_id' => $ageGroup->id,
                    'created_at' => now(),
                ]);
            }
        }
    }
}