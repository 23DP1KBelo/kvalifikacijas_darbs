<template>
  <v-app>
    <v-app-bar :elevation="2">
      <template v-slot:prepend>
      <v-app-bar-nav-icon></v-app-bar-nav-icon>
    </template>
    <v-app-bar-title>Dejotājs</v-app-bar-title>
  </v-app-bar>

  <v-navigation-drawer>
    <v-list>
      <v-list-item
        v-for="link in links"
        :key="link"
        link
        @click="handleClick(link)"
      >
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>

    <v-main>
      <v-container>
        <h1>Aktualitātes</h1>
          <v-card v-if="posts" class="mx-auto my-8" elevation="16">
            <v-card-title>{{ posts.title }}</v-card-title>
            <v-card-subtitle>Ambis un Pipars</v-card-subtitle>
            <v-card-text>{{ posts.description }}</v-card-text>
          </v-card>
      </v-container>
    </v-main>
    <v-footer class="d-flex align-center justify-center flex-wrap" color="surface-light" :style="{ maxHeight: '150px', overflowY: 'auto' }">
      <v-btn variant="text" v-for="link in links" :key="link" text rounded>{{ link }}</v-btn>
      <div class="flex-1-0-100 text-center">
        {{ new Date().getFullYear() }} <strong>Dejotajs</strong>
      </div>
    </v-footer>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const links = [
  'Sākums',
  'Deju grupas',
  'Kalendārs',
  'Uzņemšana',
  'Par mums',
  'Reģistrēšanās',
]

const posts = ref(null)

onMounted(async () => {
  const response = await fetch('/api/posts/1')
  const data = await response.json()
  posts.value = data.data
})
</script>
