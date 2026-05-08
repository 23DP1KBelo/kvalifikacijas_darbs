<?php

namespace Database\Seeders;

use App\Models\DanceGroupMember;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $leaders = DanceGroupMember::where('role', 'leader')
            ->where('status', 'approved')
            ->get();

        if ($leaders->isEmpty()) {
            return;
        }

        $postsCount = rand(40, 50);

        for ($i = 0; $i < $postsCount; $i++) {

            Post::factory()->create([
                'dance_group_member_id' => $leaders->random()->id,
            ]);
        }
    }
}
