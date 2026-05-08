<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanceGroupMember;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $eventNames = [
            "Pavasara deju festivāls",
            "Ziemas horeogrāfiju skate",
            "Rīgas deju čempionāts",
            "Jauno talantu uzstāšanās vakars",
            "Starptautiskais deju festivāls",
            "Vasaras deju nometnes noslēgums",
            "Rudens deju parāde",
            "Kolektīvu sadraudzības pasākums",
            "Deju studiju atklātais vakars",
            "Horeogrāfijas meistarklase un skate",
            "Latvijas deju diena",
            "Jauniešu deju turnīrs",
            "Skatuves mākslas festivāls",
            "Enerģijas un ritma vakars",
            "Deju izaicinājumu diena",
            "Kultūras un dejas svētki",
            "Reģionālais deju konkurss",
            "Mūsdienu deju šovs",
            "Klasiskās dejas prezentācija",
            "Urban dance battle",
            "Deju studiju kauss",
            "Komandu horeogrāfiju sacensības",
            "Radošās dejas diena",
            "Jauno horeogrāfu skate",
            "Deju apvienību festivāls",
            "Vasaras deju šovs pilsētā",
            "Ziemassvētku deju koncerts",
            "Dejas un kustības performance",
            "Baltijas deju festivāls",
            "Jauno līderu uzstāšanās",
            "Deju skolu salidojums",
            "Ritma un kustības izaicinājums",
            "Deju maratons",
            "Skatuves enerģijas festivāls",
            "Hiphop un street dance turnīrs",
            "Deju meistaru vakars",
            "Kolektīvu lielkoncerts",
            "Jauno dejotāju prezentācija",
            "Starptautiskā deju nometne",
            "Noslēguma gala koncerts"
        ];

        $eventDescriptions = [
            "Šis pasākums apvieno dažādu deju kolektīvu dalībniekus, lai kopīgi demonstrētu savu progresu un radošumu. Skatītājus sagaida daudzveidīgas horeogrāfijas, enerģiski priekšnesumi un īpaša atmosfēra. Pasākuma mērķis ir veicināt sadarbību un attīstīt jaunos deju talantus.",
            "Meistarklase un uzstāšanās vakars, kur dalībnieki varēs apgūt jaunas kustību tehnikas un uzreiz tās pielietot praksē. Pieredzējuši treneri sniegs individuālu atgriezenisko saiti, palīdzot uzlabot precizitāti, ritmu un izteiksmi uz skatuves.",
            "Šis festivāls ir veltīts jaunajiem dejotājiem, kuri vēlas parādīt savas prasmes plašākai auditorijai. Dalībnieki piedalīsies dažādos deju stilos, sākot no klasiskās dejas līdz mūsdienu horeogrāfijām, radot dinamisku un iedvesmojošu programmu.",
            "Komandu sacensības, kurās katrs kolektīvs demonstrē savu unikālo stilu un interpretāciju. Žūrija vērtēs tehniku, sinhronizāciju un māksliniecisko izpildījumu. Šis ir lielisks veids, kā attīstīt komandas garu un profesionālās prasmes.",
            "Noslēguma koncerts, kurā tiks apkopots visa sezonas darbs. Uz skatuves kāps dažādu vecuma grupu dejotāji, piedāvājot skatītājiem emocionāli bagātu un vizuāli iespaidīgu programmu, kas atspoguļo visa gada centību un progresu."
        ];

        $cities = [
            "Rīga",
            "Daugavpils",
            "Liepāja",
            "Jelgava",
            "Jūrmala",
            "Ventspils",
            "Rēzekne",
            "Valmiera",
            "Ogre",
            "Tukums",
            "Cēsis",
            "Sigulda",
            "Saldus",
            "Bauska",
            "Kuldīga",
            "Madona",
            "Alūksne",
            "Ludza",
            "Smiltene",
            "Jēkabpils"
        ];

        $leaders = DanceGroupMember::where('role', 'leader')
            ->where('status', 'approved')
            ->get();

        if ($leaders->isEmpty()) {
            return;
        }

        foreach ($leaders as $leader) {

            $eventsCount = rand(1, 3);

            for ($i = 0; $i < $eventsCount; $i++) {

                $start = Carbon::now()->addDays(rand(1, 90));
                $end = (clone $start)->addDays(rand(1, 2));

                Event::create([
                    'name' => $eventNames[array_rand($eventNames)],
                    'description' => $eventDescriptions[array_rand($eventDescriptions)],
                    'location' => $cities[array_rand($cities)],
                    'date_start' => $start,
                    'date_end' => $end,
                    'dance_group_member_id' => $leader->id, 
                ]);
            }
        }
    }
}
