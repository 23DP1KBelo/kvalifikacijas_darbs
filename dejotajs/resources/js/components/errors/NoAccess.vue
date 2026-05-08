<template>
  <v-container class="text-center mt-16">
    <v-icon size="80" color="error">mdi-lock-alert</v-icon>
    <h1 class="text-h3 mt-6">Jūs nepiederat šim kolektīvam!</h1>
    <p class="text-subtitle-1 mt-2">
      Lūdzu, pārbaudiet vai esat apstiprināts kolektīvā vai sazinieties ar kolektīva vadītāju.
    </p>
    <v-btn class="mt-6" color="primary" @click="$router.push('/')">
      Atpakaļ uz sākumu
    </v-btn>
  </v-container>
</template>