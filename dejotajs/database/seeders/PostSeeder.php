<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\DanceGroupMember;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('lv_LV');

        // Tikai leaderi
        $leaders = DanceGroupMember::where('role', 'leader')->get();

        foreach ($leaders as $leader) {
            // Nejaušs skaits postu katram leader
            $numPosts = rand(1, 5);

            for ($i = 0; $i < $numPosts; $i++) {
                Post::create([
                    'dance_group_member_id' => $leader->id,
                    'title' => $faker->sentence(6, true),
                    'description' => $faker->paragraph(3, true),
                    'private' => $faker->boolean(50),
                ]);
            }
        }
    }
}
