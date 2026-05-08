<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AppUserFactory extends Factory
{
    protected $faker;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->faker = \Faker\Factory::create('lv_LV');
    }

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'phone_number' => '+371 ' . $this->faker->numberBetween(20000000, 29999999),
        ];
    }
}
