<template>
  <v-app>
    <v-main>

      <!-- HERO SECTION -->
      <v-container fluid class="hero-section">
        <v-container class="py-16">
          <v-row align="center">
            <v-col cols="12" md="6">
              <h1 class="text-primary text-h2 font-weight-bold mb-4">
                DEJOTĀJS
              </h1>
              <p class="text-accent text-h6 mb-6">
                Kustība. Ritms. Brīvība.
              </p>
            </v-col>
            <v-col cols="12" md="6">
              <div class="hero-image" :style="{ backgroundImage: 'url(' + heroImage + ')' }"></div>
            </v-col>
          </v-row>
        </v-container>
      </v-container>

      
      <!-- Citāts par dejotāju -->
      <v-container fluid class="py-12 text-center bg-secondary">
        <div  class="text-h3 font-italic">
          "Deja ir brīvība, kas izpaužas caur kustību"
        </div>
      </v-container>

      <!-- Aktualitātes -->
      <v-container fluid class="py-16 bg-softblue">
        <v-container>
          <h2 class="text-center text-text mb-10 text-h3">Aktualitātes</h2>

          <v-row>
            <v-col
              v-for="post in latestPosts"
              :key="post.id"
              cols="12"
              md="4"
            >
              <v-card class="pa-0" elevation="4" height="400">
                <!-- Attēls -->
                <v-img v-if="post.picture" :src="post.picture" height="200px" cover></v-img>

                <!-- Teksts zem attēla -->
                <v-card-subtitle class="mt-4 px-4 text-subtitle-2">
                  {{ post.dance_group_member?.dance_group?.name || 'Nezināms kolektīvs' }}
                </v-card-subtitle>
                <v-card-title class="px-4 text-accent">{{ post.title }}</v-card-title>
                <v-card-text class="px-4 pb-4 text-body-1">
                  {{ post.description || ' ' }}
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>

          <v-container fluid class="d-flex justify-center">
            <v-btn color="primary" class="mt-4" @click="$router.push('/posts')">
              Skatīt visas aktualitātes
            </v-btn>
          </v-container> 
        </v-container>
      </v-container>

    </v-main>
  </v-app>
</template>

<style>
.hero-section {
  background: #F6FAFD;
}

.hero-image { 
  height: 400px; 
  border-radius: 24px; 
  background-size: cover; 
  background-position: center; 
}

.fixed-card {
  display: flex;
  flex-direction: column;
  height: 300px;
}

.card-text {
  flex-grow: 1;
  overflow: hidden;
}
</style>

<script>
import heroImage from '@/assets/img/hero.png'
import axios from 'axios'

export default {
  name: 'LatestPosts',
  data() {
    return {
      heroImage,
      posts: [],
    }
  },
  computed: {
    latestPosts() {
      if (!this.posts || this.posts.length === 0) return []
      return this.posts.slice(0, 3)
    }
  },
  methods: {
    async fetchLatestPosts() {
      try {
        const res = await axios.get('/api/posts')
        this.posts = Array.isArray(res.data) ? res.data : (res.data.posts || res.data.data || [])
        console.log('Ielādētie post:', this.posts)
      } catch (err) {
        console.error('Kļūda ielādējot postus:', err)
        this.posts = []
      }
    }
  },
  mounted() {
    this.fetchLatestPosts()
  }
}
</script>