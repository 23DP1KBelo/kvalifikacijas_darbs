<?php

namespace Database\Seeders;

use App\Models\AgeGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use App\Models\AppUser;
use App\Models\DanceGroup;
use App\Models\DanceGroupMember;
use Database\Seeders\DanceGroupMemberSeeder;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        AppUser::factory()->count(100)->create();
        $this->call(DanceGroupSeeder::class);
        AgeGroup::factory()->count(70)->create();
        $this->call(AdmissionSeeder::class);
        $this->call(DanceGroupMemberSeeder::class);
        $this->call(PostSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(DanceGroupsInEventsSeeder::class);
    }
}
