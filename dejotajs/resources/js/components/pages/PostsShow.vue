<template>
  <v-container fluid class="pa-0">
    <v-row class="d-flex justify-center" no-gutters>

      <v-col
        cols="12"
        v-for="post in posts"
        :key="post.id"
        class="d-flex justify-center"
      >
        <v-card class="mx-4 my-6" elevation="16" color="softblue" width="600">


          <div class="d-flex justify-end pa-2">
            <v-btn v-if="isAdmin" icon="mdi-delete" variant="text" color="red" @click="deletePost(post.id)"/>
          </div>

          <v-img :src="post.picture || `https://picsum.photos/id/${post.id}/600/350`" height="350px" cover alt="img_kolektīvs"/>

          <v-card-text class="px-4 py-4">
            <v-card-title class="text-h5 font-weight-bold">
              {{ post.title }}
            </v-card-title>

            <v-card-subtitle class="mb-2">
              {{ post.dance_group_member?.dance_group?.name || 'Nezināms kolektīvs' }}
            </v-card-subtitle>

            <v-card-text class="text-body-1">
              {{ post.description || '' }}
            </v-card-text>
          </v-card-text>

          <v-card-subtitle class="d-flex justify-end px-4 pb-4 text-caption">
            {{ formatDate(post.created_at) }}
          </v-card-subtitle>

        </v-card>
      </v-col>

    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      posts: []
    }
  },

  computed: {
    isAdmin() {
      const user = JSON.parse(localStorage.getItem('user'))
      return user?.role === 'admin'
    }
  },

  mounted() {
    this.fetchPosts()
  },

  methods: {

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
</style>