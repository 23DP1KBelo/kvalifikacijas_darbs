<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppUser;
use App\Models\DanceGroup;
use App\Models\AgeGroup;
use App\Models\DanceGroupMember;
use Faker\Factory as Faker;

class DanceGroupMemberSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('lv_LV');

        $users = AppUser::where('role', 'user')->get(); 
        $groups = DanceGroup::all();

        foreach ($groups as $group) {

            $ageGroups = AgeGroup::where('dance_group_id', $group->id)->get();
            $leader = $users->random();

            DanceGroupMember::create([
                'user_id' => $leader->id,
                'dance_group_id' => $group->id,
                'age_group_id' => $ageGroups->random()?->id,
                'role' => 'leader',
                'status' => 'approved',
            ]);

            $availableUsers = $users->where('id', '!=', $leader->id);
            $numDancers = min(rand(5,10), $availableUsers->count());
            $dancers = $availableUsers->random($numDancers);

            foreach ($dancers as $dancer) {

                DanceGroupMember::create([
                    'user_id' => $dancer->id,
                    'dance_group_id' => $group->id,
                    'age_group_id' => $ageGroups->random()?->id,
                    'role' => 'dancer',
                    'status' => collect(['approved','waiting'])->random(),
                ]);
            }
        }
    }
}