<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $dir = storage_path('app/public/posts');

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = uniqid() . '.jpg';

        copy(
            "https://picsum.photos/640/480",
            $dir . '/' . $fileName
        );

        $description = $posts = [
        "Šodien mūsu deju grupa aizvadīja intensīvu treniņu, gatavojoties nākamajam uzstāšanās pasākumam. Strādājām pie precizitātes, sinhronizācijas un izteiksmes uzlabošanas, lai katrs kustību elements izskatītos vienots un spēcīgs. Liels paldies visiem dalībniekiem par enerģiju un disciplīnu treniņa laikā!",
        "Aizvadītajā nedēļas nogalē mūsu kolektīvs piedalījās meistarklasē, kur apguvām jaunas horeogrāfijas tehnikas un kustību kombinācijas. Treneri uzsvēra ritma izjūtas un ķermeņa kontroles nozīmi. Šāda pieredze palīdz mums augt kā komandai un attīstīt individuālās prasmes augstākā līmenī.",
        "Šis ieraksts ir veltīts mūsu lieliskajai komandai, kas katru dienu iegulda darbu un laiku, lai kļūtu labāki. Dejošana nav tikai kustība, tā ir arī emocijas, disciplīna un komandas gars. Mēs turpinām trenēties ar mērķi sasniegt augstāko kvalitāti nākamajās uzstāšanās reizēs.",
        "Mūsu jaunākā horeogrāfija ir gandrīz pabeigta, un mēs esam ļoti lepni par rezultātu. Katrs mēģinājums palīdz uzlabot detaļas un pārliecību uz skatuves. Drīzumā plānojam prezentēt šo priekšnesumu publikai, un ceram uz lielisku skatītāju atsaucību un atbalstu.",
        "Treniņš šodien bija īpaši intensīvs, jo strādājām pie sarežģītām kustību kombinācijām un pāreju precizitātes. Lai gan bija izaicinoši, komandas motivācija palīdzēja pārvarēt grūtības. Katrs solis tuvina mūs mūsu mērķim - radīt profesionālu un emocionāli spēcīgu priekšnesumu.",
        ];

        $titles = ["Jaunais treniņu cikls ir sācies","Meistarklase ar īpašu viestreneri","Mūsu jaunākā horeogrāfija progresē",
            "Komandas saliedēšanās diena","Intensīvs treniņš pirms uzstāšanās","Jauni soļi un kustību kombinācijas","Sezonas pirmā uzstāšanās",
            "Ritma un tehnikas uzlabošana","Enerģijas pilns vakara treniņš","Deju studijas aizkulises","Jauni mērķi šai sezonai",
            "Komandas progress katru dienu","Sagatavošanās lielajam pasākumam","Horeogrāfijas pēdējie labojumi","Motivācijas pilns treniņš",
            "Jauni izaicinājumi un iespējas","Uzstāšanās dienas emocijas","Kopā mēs esam stiprāki","Tehnikas un precizitātes treniņš","Deju ceļš uz pilnību"
        ];

        return [
            'title' => $this->faker->randomElement($titles),
            'description' => $this->faker->randomElement($description),
            'picture' => null,
            'private' => $this->faker->boolean(),
            'dance_group_member_id' => null, 
        ];
    }
}
