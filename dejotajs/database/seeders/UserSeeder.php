<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppUser;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('12345678');
        $faker = Faker::create('lv_LV');

        AppUser::create([
            'name' => 'Ambera',
            'surname' => 'Gausiņa',
            'email' => 'ambis@gmail.com',
            'password' => Hash::make('Menestins,1'),
            'phone_number' => '+37129292924',
            'role' => 'admin',
        ]);

         for ($i = 1; $i <= 10; $i++) {
            AppUser::create([
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'email' => strtolower($faker->firstName.'.'.$faker->lastName.$i.'@test.lv'),
                'password' => $password,
                'phone_number' => $faker->phoneNumber(),
                'role' => 'user'
            ]);
        }
    }
}
