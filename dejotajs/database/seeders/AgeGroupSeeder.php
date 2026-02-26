<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanceGroup;
use App\Models\AgeGroup;

class AgeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $danceGroups = DanceGroup::all();

        $ageGroupsData = [
            ['name' => 'Bērni', 'age_group' => '6-10 gadi', 'status_admission' => true],
            ['name' => 'Jaunieši', 'age_group' => '11-16 gadi', 'status_admission' => true],
            ['name' => 'Pieaugušie', 'age_group' => '17+ gadi', 'status_admission' => false],
        ];

        foreach ($danceGroups as $group) {

            // Katrai grupai izveido 2-3 age_groups
            $numGroups = rand(2, 3);
            $selectedAgeGroups = collect($ageGroupsData)->random($numGroups);

            foreach ($selectedAgeGroups as $ageGroup) {
                AgeGroup::create([
                    'name' => $ageGroup['name'],
                    'age_group' => $ageGroup['age_group'],
                    'status_admission' => $ageGroup['status_admission'],
                    'dance_group_id' => $group->id,
                ]);
            }
        }
    }
}
