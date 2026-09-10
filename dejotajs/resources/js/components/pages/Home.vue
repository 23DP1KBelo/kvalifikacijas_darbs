<template>
  <v-app>
    <v-main>
      <v-row class="ma-0" style="height: 100vh;">
        <!-- RAKSTI -->
        <v-col
          cols="12"
          md="9"
          class="pa-4 d-flex flex-column"
          style="height: 100%;"
        >
          <h2 class="text-center text-accents mb-7">
            JAUNĀKIE RAKSTI
          </h2>
          <div class="overflow-y-auto flex-grow-1 mt-3">
            <v-card
              v-for="post in latestPosts"
              :key="post.id"
              class="bg-primary elevation-3 mb-8 pa-6 pa-md-8 mx-auto d-flex flex-column justify-center"
              width="600"
              height="650"
              max-width="90%"
            >
              <h3 class="text-right mb-6">
                {{ post.dance_group_member?.dance_group?.name || 'Nezināms kolektīvs' }}
              </h3>
              <v-img
                :src="post.picture || `https://picsum.photos/id/${post.id}/600/350`"
                height="250"
                width="100%"
                contain
                class="d-flex justify-center align-center"
              />
              <v-card-subtitle class="text-right text-black mt-2">
                {{ formatDate(post.created_at) }}
              </v-card-subtitle>
              <v-card-text class="text-center text-black text-body-1 mt-4 text-para">
                {{ post.description || '' }}
              </v-card-text>
            </v-card>
          </div>
        </v-col>
        <!-- PASĀKUMI -->
        <v-col
          cols="3"
          class="d-none d-md-block pa-4"
          style="height: 100%;"
        >
          <div class="h-100">

            <h2 class="text-accent text-center text-accents">
              APMEKLĒ!
            </h2>
            <v-list class="pa-4 mt-10">
              <v-list-item
                v-for="event in upcomingEvents"
                :key="event.id"
                class="border-b hover-effect"
              >
                  <v-list-item-title class="text-h6 text-wrap">
                    {{ formatDate(event.date_start) }} -
                    {{ event.name || 'Nezināms pasākums' }}
                  </v-list-item-title>

                  <v-list-subheader class="text-right">
                    {{ event.location || 'Nezināma vieta' }}
                  </v-list-subheader>
              </v-list-item>
            </v-list>
            <div class="d-flex justify-center">
              <v-btn
                rounded="lg"
                class="bg-accent mt-6 text-none text-small letter-spacing-0"
                @click="$router.push('/calender')"
              >
                Uzzināt vairāk!
              </v-btn>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-main>
  </v-app>
</template>

<style>
  .text-accents {
    font-size: 48px;
  }
  .text-small {
    font-family: "Playfair Display", serif;
  }
  .text-para {
    font-size: 12px;
    font-family: "Inter", sans-serif;
  }
  .hover-effect{
    transition: all 0.3s ease;
  }
  .hover-effect:hover {
    background-color: #F7FAFF;
    transform: translateX(-15px);
    border-radius: 8px;
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
      events: [],
    }
  },
  computed: {
    latestPosts() {
      if (!this.posts || this.posts.length === 0) return []
      return this.posts.slice(0, 10) // Atgriež pēdējos 10 ierakstus, ja tie eksistē
    },
    upcomingEvents() {
      if (!this.events || this.events.length === 0) return []
      return this.events.slice(0, 3) // Atgriež pēdējos 4 pasākumus, ja tie eksistē
    }
  },
  methods: {
    async fetchData() {
        try {
          const [postsRes, eventsRes] = await Promise.all([
            axios.get('/api/posts'),
            axios.get('/api/events')
          ])

          this.posts = Array.isArray(postsRes.data) ? postsRes.data : (postsRes.data.posts || postsRes.data.data || [])
          this.events = Array.isArray(eventsRes.data) ? eventsRes.data : (eventsRes.data.posts ||eventsRes.data.data || [])

        } catch (err) {
           console.error('Kļūda ielādējot datus:', err)
            this.posts = []
            this.events = []

        }
    },
    formatDate(date) {
    return new Date(date).toLocaleDateString('lv-LV', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    })
  }

  },
  mounted() {
    this.fetchData()
  }
}
</script>