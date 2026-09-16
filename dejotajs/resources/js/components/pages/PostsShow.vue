<template>
    <v-container fluid class="py-10">
      <div class="d-flex justify-center flex-column mb-10">
        <h1 class="text-center text-accents">RAKSTI</h1>
        <p class="text-subhed text-center">Kolektīvu pasākumi, intereses un atpūta</p>
      </div>
      <v-container>
      <v-row>
        <v-col
          v-for="post in paginatedPosts"
          :key="post.id"
          cols="12"
          sm="6"
          lg="3"
          class="d-flex"
        >
          <v-card
            class="w-100 d-flex flex-column elevation-3 rounded-0 card-hover"
            height="430"
          >
            <!-- Bilde -->
            <v-img
              :src="
                post.picture ||
                `https://picsum.photos/id/${post.id}/600/350`
              "
              height="140"
              cover
              alt="Kolektīva attēls"
            />
            <!-- Kolektīva nosaukums -->
            <h4
              class="text-right pt-4 px-4"
            >
              {{
                post.dance_group_member?.dance_group?.name ||
                'Nezināms kolektīvs'
              }}
            </h4>
            <!-- Raksta nosaukums -->
            <h2
              class="font-weight-bold text-left px-4 text-accent"
            >
              {{ post.title }}
            </h2>
            <!-- Raksta apraksts -->
            <v-card-text
              class="text-body-1 px-4 line-clamp-3 overflow-hidden text-para"
              style="max-height: 65px;"
            >
              {{ post.description || '' }}
            </v-card-text>
            <v-spacer />
            <!-- Datums, poga -->
            <v-card-actions
              class="d-flex justify-space-between align-center px-4 pb-4"
            >
              <span class="text-caption text-medium-emphasis">
                {{ formatDate(post.created_at) }}
              </span>
              <v-btn
                icon="mdi-arrow-top-right"
                variant="text"
                @click="openArticle(post)"
              />
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <!-- Paģinācija -->
    <div class="d-flex justify-center mt-8">
      <v-pagination
        v-model="page"
        :length="pageCount"
        :total-visible="5"
        rounded="circle"
      />
    </div>
    <v-dialog
      v-model="dialog"
      max-width="1100"
    >
      <v-card
        class="rounded-0 order-last order-md-first"
        min-height="600"
      >
        <v-row no-gutters class="h-100">
          <v-col
            cols="12"
            md="6"
            class="d-flex flex-column pa-8"
          >
            <!-- Atpakaļ poga -->
            <div class="d-flex justify-start">
              <v-btn
                variant="text"
                prepend-icon="mdi-arrow-left"
                @click="dialog = false"
              >
              </v-btn>
            </div>
            <!-- Kolektīva nosaukums -->
            <h4 class="mt-4">
              <div class="text-subtitle-1 text-medium-emphasis text-right">
                {{
                  selectedPost?.dance_group_member?.dance_group?.name ||
                  'Nezināms kolektīvs'
                }}
              </div>
            </h4>
            <!-- Pilnais teksts -->
            <div class="flex-grow-1 d-flex align-center py-8">
              <div class=" text-para text-black">
                {{ selectedPost?.description || '' }}
              </div>
            </div>
            <!-- Datums -->
            <div class="text-caption text-medium-emphasis">
              {{ formatDate(selectedPost?.created_at) }}
            </div>
          </v-col>
          <!-- Labā puse -->
          <v-col
            cols="12"
            md="6"
            class="d-flex flex-column pa-8 order-first order-md-last"
          >
            <!-- Raksta nosaukums -->
            <h2 class="text-accent text-accents mt-6 text-uppercase text-center">
              {{ selectedPost?.title }}
            </h2>
            <!-- Bilde -->
            <v-img
              :src="
                selectedPost?.picture ||
                `https://picsum.photos/id/${selectedPost?.id}/600/350`
              "
              width="100%"
              height="350"
              contain
              alt="Raksta_attels"
            />
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      posts: [],
      page: 1,
      postsPerPage: 8,
      dialog: false,
      selectedPost: null,
    }
  },

  computed: {
    isAdmin() {
      const user = JSON.parse(localStorage.getItem('user'))
      return user?.role === 'admin'
    },
    paginatedPosts() {
      const start = (this.page - 1) * this.postsPerPage
      const end = start + this.postsPerPage

      return this.posts.slice(start, end)
    },
    pageCount() {
      return Math.ceil(this.posts.length / this.postsPerPage)
    },
  },

  mounted() {
    this.fetchPosts()
  },

  methods: {
    openArticle(post) {
      this.selectedPost = post
      this.dialog = true
    },
    async fetchPosts() {
      try {
        const response = await fetch('/api/posts')
        const data = await response.json()

        this.posts = Array.isArray(data.data)
          ? data.data
          : []
      } catch (err) {
        console.error('Kļūda ielādējot ierakstus:', err)
        this.posts = []
      }
    },

    async deletePost(postId) {
      try {
        await axios.delete(`/api/posts/admin/${postId}`, {
          withCredentials: true
        })

        this.posts = this.posts.filter(p => p.id !== postId)

        alert('Ieraksts veiksmīgi dzēsts')
      } catch (err) {
        console.error('Kļūda dzēšot ierakstu:', err)
        alert('Neizdevās dzēst ierakstu')
      }
    },

    formatDate(dateStr) {
      if (!dateStr) return ''

      return new Date(dateStr).toLocaleDateString('lv-LV', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      })
    }
  }
}
</script>

<style>
.fill-height {
  min-height: 100vh;
}

.text-subhed{
  font-family: "Inter", sans-serif;
  font-weight:400;
  margin: 0;
  padding: 0;
}

.text-playFair {
  font-family: "Playfair Display", serif;
}

.text-para {
    font-size: 14px;
    font-family: "Inter", sans-serif;
    font-weight: 300;
  }

.card-hover{
  transition: all 0.3s ease;
}

.card-hover:hover{
  transform: translateY(-25px);
  border-radius: 8px;
}
</style>