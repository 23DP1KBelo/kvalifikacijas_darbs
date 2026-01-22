<template>
  <v-app>
    <v-app-bar :elevation="2" class="bg-primary" >
      <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title class="app-bar-title text-center">Dejotājs</v-app-bar-title>
      <v-btn icon @click="toggleTheme">
        <v-icon>
          {{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}
        </v-icon>
      </v-btn>
  </v-app-bar>

  <v-navigation-drawer      
    v-model="drawer"
    :location="$vuetify.display.mobile ? 'bottom' : undefined"
    temporary>
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
        <h1 class="text-center">Aktualitātes</h1>
          <v-card v-for="post in posts" :key="post.id" class="mx-auto my-8 pb-7" color="secondary" elevation="16"  >
            <v-card-title class="text-h4">{{ post.title }}</v-card-title>
            <v-card-subtitle>{{ post.dance_group_member?.dance_group?.name}}</v-card-subtitle>
            <v-card-text class="text-body-1">{{ post.description }}</v-card-text>
            <br>
            <v-card-subtitle class="d-flex justify-end text-subtitle-2">{{ new Date(post.created_at).toLocaleDateString()  }}</v-card-subtitle>
          </v-card>
      </v-container>
    </v-main>
    <v-footer class="d-flex align-center justify-center flex-wrap ga-sm-6" color="primary" :style="{overflowY: 'auto'} ">
      <div v-if="!$vuetify.display.mobile" >
        <v-btn variant="text" v-for="link in links" :key="link" text rounded>{{ link }}</v-btn>
      </div>
      <div class="flex-1-0-100 text-center d-flex justify-center ga-sm-4">
        {{ new Date().getFullYear() }} <strong>Dejotajs</strong>
      </div>
    </v-footer>
  </v-app>
</template>

<script setup>
import { watch } from 'vue'
import { ref, onMounted } from 'vue'
import { computed } from 'vue'
import { useTheme } from 'vuetify'

const theme = useTheme()

const isDark = computed(() => theme.global.current.value.dark)

function toggleTheme () {
  theme.global.name.value = isDark.value ? 'light' : 'dark'
}

  const drawer = ref(false)
  const group = ref(null)

  watch(group, () => {
    drawer.value = false
  })

  const links = [
    'Sākums',
    'Deju grupas',
    'Kalendārs',
    'Uzņemšana',
    'Reģistrēšanās',
  ]

  const posts = ref(null)

  onMounted(async () => {
    const response = await fetch('/api/posts')
    const data = await response.json()
    posts.value = data.data
  })

</script>
