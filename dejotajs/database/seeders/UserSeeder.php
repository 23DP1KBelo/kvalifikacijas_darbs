<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AppUser;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppUser::create([
            'name' => 'Ambis',
            'surname' => 'Gausiņa',
            'email' => 'ambera@gmail.com',
            'password' => Hash::make('12345678'),
            'phone_number' => '+37112345678',
            'role' => 'user',
        ]);
    }
}
