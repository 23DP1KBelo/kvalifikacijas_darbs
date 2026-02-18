<template>
    <h1 class="text-center mt-8">Kolektīva vadītājs</h1>
    <br>
    <div class="d-flex justify-center ga-8">
        <v-btn color="primary" @click="$router.push('/danceGroup-list')">Jau esošā kolektīvā</v-btn>
        <v-btn color="primary" @click="$router.push('/danceForm')">Jaunā kolektīvā</v-btn>
    </div>
</template>
