<?php

namespace Database\Factories;

use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgeGroup>
 */
class AgeGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        $ageGroups = ['5-7 gadi', '8-10 gadi', '11-13 gadi', '14-16 gadi', '17+', 'jaunieši', 'pieaugušie', 'visi vecumi', '2-4', '18-24 gadi', '25-34 gadi', '35-44 gadi', '45-54 gadi', '55-64 gadi', '65+ gadi', '18-20 gadi'];
        $names = ["mežs","koks","ozols","Bērzs","Priede","Egle","Lapa","Zars","Sakne","Zāle","Pļava","Zieds","Roze","Margrietiņa","Pienenes",
        "Sūna","Akmens","Kalns","Upe","Ezers","Jūra","Okeāns","Strauts","Ūdenskritums","Debesis","Mākonis","Saule","Mēness","Zvaigzne","Vējš",
        "Vētra","Lietus","Sniegs","Krusa","Migla","Rasa","Daba","Mežsargs","Dzīvnieki","Putni"
        ];

        return [
            'name' => $this->faker->randomElement($names) . ' ' . $this->faker->randomElement($names),
            'age_group' => $this->faker->randomElement($ageGroups),
            'status_admission' => $this->faker->boolean(),
            'dance_group_id' => $this->faker->numberBetween(1, 40),
        ];
    }
}
