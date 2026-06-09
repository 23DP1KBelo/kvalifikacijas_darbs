# Dejotājs
>Tīmekļa vietne deju kopienai un to pasākumu publicēšanai.

Mūsdienās digitālās tehnoloģijas arvien vairāk tiek izmantotas arī kultūras un brīva laika jomās, tostarp dejošanā. Deju kolektīvi, dejotāji un deju interesanti izmanto dažādas platformas, lai iegūtu informāciju, sazinātos un organizētu savu darbību. Tomēr šobrīd šī informācija ir sadrumstalota un pieejama vairākas atsevišķās sistēmās, kas apgrūtina informācijas meklēšanu un nodošanu deju interesantiem. Līdz ar to rodas nepieciešamība pēc vietas tīmekļa sistēmas, kas būtu pielāgota tieši deju nozarei.

## Tehnoloģijas
### Frontend
- HTML;
- CSS;
- Vue 3;
- Chart.js.

### Backend
- PHP;
- Laravel 13;
- MySQL;


## Aplikācijas startēšana
### Backend startēšana:
```sh 
composer i --ignore-platform-reqs
cp .env.example .env
./vendor/bin/sail up -d
```
### Frontend startēšana:
```sh 
npm i
npm run dev
```
## Testa piemēri
**Testa dati – Reģistrācijas forma (Kolektīva nosaukums)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | - | Kolektīva nosaukuma lauks ir obligāts |
| 2 | aaaa | - |
| 3 | Šis ir testa teksts, kas paredzēts validācijas pārbaudei. Tā mērķis ir pārsniegt noteikto divsimt piecdesmit piecu rakstzīmju ierobežojumu, lai pārliecinātos, vai sistēma korekti attēlo kļūdas paziņojumu un neļauj saglabāt nederīgus datus datu bāzē. | Kolektīva nosaukumam jābūt ne vairāk kā 255 rakstzīmēm. |

**Testa dati – Reģistrācijas forma (Kolektīva apraksts)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | - | Lūdzu, uzrakstiet informāciju par kolektīvu |
| 2 | aaaa | - |
| 3 | Šis ir garāks testa teksts, kas paredzēts validācijas mehānisma pārbaudei tīmekļa vietnes ievades formā. Teksts speciāli pārsniedz piecsimt rakstzīmju ierobežojumu, lai pārbaudītu, vai sistēma korekti apstrādā nederīgus ievaddatus un nepieļauj to saglabāšanu datu bāzē. Šādas pārbaudes ir būtiskas, lai nodrošinātu datu kvalitāti, novērstu pārlieku garu ierakstu ievadi un aizsargātu sistēmu pret neparedzētu datu apstrādi. Ja validācija darbojas korekti, lietotājam ir jāsaņem kļūdas paziņojums. | Informācija par kolektīvu nevar būt garāka par 500 rakstzīmēm. |

**Testa dati – Reģistrācijas forma (Kolektīva pilsēta)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | - | Lūdzu, norādiet kolektīva pilsētu. |
| 2 | Rīga | - |
| 3 | Šis ir testa teksts, kas paredzēts validācijas pārbaudei. Tā mērķis ir pārsniegt noteikto divsimt piecdesmit piecu rakstzīmju ierobežojumu, lai pārliecinātos, vai sistēma korekti attēlo kļūdas paziņojumu un neļauj saglabāt nederīgus datus datu bāzē. | Pilsētas lauks nevar būt garāks par 255 rakstzīmēm. |

**Testa dati – Reģistrācijas forma (Kolektīva adrese)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | - | Lūdzu, norādiet kolektīva adresi. |
| 2 | Salaspils iela 89 | - |
| 3 | Šis ir testa teksts, kas paredzēts validācijas pārbaudei. Tā mērķis ir pārsniegt noteikto divsimt piecdesmit piecu rakstzīmju ierobežojumu, lai pārliecinātos, vai sistēma korekti attēlo kļūdas paziņojumu un neļauj saglabāt nederīgus datus datu bāzē. | Adreses lauks nevar būt garāks par 255 rakstzīmēm. |

**Testa dati – Reģistrācijas forma (Kolektīva žanrs)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | - | Lūdzu, norādiet kolektīva žanru. |
| 2 | Tautas dejas | - |

**Testa dati – Reģistrācijas forma (Kolektīva attēls)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | Nepievienots | - |
| 2 | Pievienots | - |
| 3 | Pievienots dokuments | Attēla failam ir jābūt šādos formātos: jpg, jpeg, png. |

**Testa dati – Reģistrācijas forma (Kolektīva apstiprinājuma dokuments)**

| Testa ID | Ieejas dati | Izejas dati |
|-----------|-------------|-------------|
| 1 | Nepievienots | Lūdzu pievienojiet dokumentu, kas apliecina, ka kolektīvs pastāv. |
| 2 | Pievienots | - |

