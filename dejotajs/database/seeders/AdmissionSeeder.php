<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admission;
use App\Models\AgeGroup;
use Carbon\Carbon;

class AdmissionSeeder extends Seeder
{
    public function run(): void
    {
        $ageGroups = AgeGroup::where('status_admission', true)->get();

        if ($ageGroups->isEmpty()) {
            return;
        }

        $names = [
            "Jauno dalībnieku uzņemšana",
            "Rudens uzņemšana",
            "Pavasara uzņemšana",
            "Atvērto durvju uzņemšana",
            "Sezonas sākuma uzņemšana",
            "Papildus uzņemšana",
        ];

        foreach ($ageGroups as $ageGroup) {

            $start = Carbon::now()->addDays(rand(1, 60));
            $end = (clone $start)->addDays(rand(5, 14));

            Admission::create([
                'name' => $names[array_rand($names)],
                'start_date' => $start,
                'end_date' => $end,
                'age_group_id' => $ageGroup->id,
            ]);
        }  
    }
}
