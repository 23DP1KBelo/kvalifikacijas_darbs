<?php
namespace Database\Factories;

use App\Models\DanceGroup;
use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class DanceGroupMemberFactory extends Factory
{
    protected $model = \App\Models\DanceGroupMember::class;

    public function definition(): array
    {
        return [
            'dance_group_id' => DanceGroup::inRandomOrder()->value('id'),
            'user_id' => $this->faker->numberBetween(2, 100),
            'role' => $this->faker->randomElement(['dancer', 'leader']),
            'status' => $this->faker->randomElement(['approved', 'declined', 'waiting']),
            'age_group_id' => null, 
        ];
    }
}

