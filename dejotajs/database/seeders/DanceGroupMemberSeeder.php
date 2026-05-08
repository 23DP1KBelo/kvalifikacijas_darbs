<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanceGroup;
use App\Models\DanceGroupMember;

class DanceGroupMemberSeeder extends Seeder
{
    public function run(): void
    {
        $groups = DanceGroup::all();

        foreach ($groups as $group) {

            $leadersCount = rand(1, 3);

            for ($i = 0; $i <= $leadersCount; $i++) {

                DanceGroupMember::create([
                    'dance_group_id' => $group->id,
                    'user_id' => rand(2, 100),
                    'role' => 'leader',
                    'status' => 'approved',
                    'age_group_id' => null,
                ]);
            }

            if ($group->status === 'approved') {

                $ageGroups = $group->ageGroups;

                if ($ageGroups->isNotEmpty()) {

                    $dancersCount = rand(10, 15);

                    for ($i = 0; $i <= $dancersCount; $i++) {

                        DanceGroupMember::create([
                            'dance_group_id' => $group->id,
                            'user_id' => rand(2, 100),
                            'role' => 'dancer',
                            'status' => 'approved',
                            'age_group_id' => $ageGroups->random()->id,
                        ]);
                    }
                }
            }
        }
    }
}   