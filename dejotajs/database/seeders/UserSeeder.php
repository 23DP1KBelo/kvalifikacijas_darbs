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
            'name' => 'Admin',
            'surname' => '1',
            'email' => 'admin@dejotajs.com',
            'password' => Hash::make('Menestins,1'),
            'phone_number' => '+37129292924',
            'role' => 'admin',
        ]);
    }
}
