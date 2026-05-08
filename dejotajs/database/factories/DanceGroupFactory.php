<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DanceGroupFactory extends Factory

{
    public function definition(): array
    {
        $dir = storage_path('app/public/dance_groups');

        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = uniqid() . '.jpg';

        copy(
            "https://picsum.photos/640/480",
            $dir . '/' . $fileName
        );

        $names = ["Dzintara Pērle","Saulgrieži","Lielupes Raksti","Vēja Brāzma","Auseklītis","Rīta Rasas","Jautrie Soļi",
        "Ozola Zari","Zemgales Ritmi","Daugavas Vilnis","Skanīgais Dejas Solis","Līgo Raksti","Zelta Vējš","Sēlijas Pērles",
        "Rīgas Pērkons","Meža Dejas","Gaismas Avots","Baltie Ziedi","Skrejošais Ritms","Saules Bērni","Tautas Raksti",
        "Kustību Vētra","Rūdolfa Soļi","Jūras Vilki","Sirds Ritmi","Lielā Deja","Vēja Ziedi","Ziemeļu Raksti","Daugavas Pērles",
        "Rotaļu Prieks","Ozolzīles","Saulainie Soļi","Pērkona Deja","Smaida Raksti","Rīgas Rotaļnieki","Līgo Dzirkstis",
        "Mēness Takas","Zemgales Ziedi","Vidzemes Vilnis","Dejas Dzirkstele"];

        $cities = [
            'Rīga', 'Liepāja', 'Daugavpils', 'Jelgava',
            'Ventspils', 'Valmiera', 'Jūrmala', 'Rēzekne'
        ];

        $streets = ["Ozolu","Saules","Brīvības","Daugavas","Liepu","Rīgas","Zemgales",
        "Vidzemes","Lāčplēša","Pļavu","Jūras","Meža","Kalnu","Parka","Skolas","Stacijas",
        "Ezera","Tirgus","Ziedu","Upes"
        ];

        $genres = [
            'lyrical dance',
            'ballet',
            'contemporary dance',
            'folk dance',
            'hip hop',
            'other'
        ];

        $descriptions = [
        'Deju kolektīvs ir cilvēku grupa, kas regulāri sanāk kopā, lai apgūtu un izpildītu dejas. Mēģinājumos tiek attīstīta ritma izjūta, koordinācija un tehnika, kā arī veidota spēja sadarboties komandā. Dalībnieki gatavo priekšnesumus dažādiem koncertiem un pasākumiem, iegūstot uzstāšanās pieredzi un pārliecību uz skatuves, kā arī attīstot disciplīnu un fizisko sagatavotību.',
        'Mēģinājumu process deju kolektīvā ietver jaunu horeogrāfiju apguvi, kustību pilnveidošanu un sinhronitātes trenēšanu. Tas palīdz attīstīt neatlaidību, uzmanību un spēju strādāt komandā, jo katra dalībnieka kustības ietekmē kopējo rezultātu. Regulārs darbs veicina arī fizisko izturību un pašdisciplīnu, kas noder gan uz skatuves, gan ikdienā.',
        'Deju kolektīvi bieži piedalās koncertos, svētkos un kultūras pasākumos, kur pārstāv savu skolu, pilsētu vai organizāciju. Uzstāšanās sniedz iespēju parādīt ieguldīto darbu, iegūt skatuves pieredzi un attīstīt pārliecību. Šie pasākumi veicina arī kultūras tradīciju saglabāšanu un kopienas saliedētību.',
        'Deju apguves laikā dalībnieki attīsta ne tikai kustību prasmes, bet arī atmiņu un koncentrēšanās spējas, jo jāatceras sarežģītas soļu kombinācijas un jāpielāgojas grupas ritmam. Tas palīdz uzlabot domāšanas ātrumu un koordināciju, kā arī spēju strādāt precīzi un sinhroni ar citiem dejotājiem.',
        'Deju kolektīvs ir arī sociāla vide, kur cilvēki veido draudzības un kopīgas pieredzes. Kopā pavadītais laiks mēģinājumos un koncertos stiprina komandas sajūtu un piederību. Tas rada atbalstošu vidi, kur dalībnieki var attīstīt savas prasmes, gūt prieku no dejas un piedzīvot kopīgus panākumus.'
        ];

        return [
            'name' => $this->faker->randomElement($names).' '. $this->faker->randomElement(['deju kolektīvs', 'deju studija', 'deju grupa']),
            'description' => $this->faker->randomElement($descriptions),
            'city' => $this->faker->randomElement($cities),
            'address' => $this->faker->randomElement($streets) . ' iela ' . rand(1, 120),
            'picture' => 'dance_groups/' . $fileName,
            'approval' => 'dance_groups/' . $fileName,
            'status' => $this->faker->randomElement(['approved', 'waiting', 'declined']),
            'genre' => $this->faker->randomElement($genres),
        ];
    }
}
