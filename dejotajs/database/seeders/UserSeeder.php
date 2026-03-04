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
            'name' => 'Admin',
            'surname' => '1',
            'email' => 'admin@dejotajs.com',
            'password' => Hash::make('Menestins,1'),
            'phone_number' => '+37129292924',
            'role' => 'admin',
        ]);

    }
}
