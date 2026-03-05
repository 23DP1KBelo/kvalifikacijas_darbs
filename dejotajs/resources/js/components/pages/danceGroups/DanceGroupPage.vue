<template>
  <v-container fluid class="pa-0">
    <!-- Grupas nosaukums + poga -->
    <v-row no-gutters class="d-flex justify-center align-center py-6">
      <v-col cols="12" md="8" class="d-flex flex-column align-center">
        <div class="d-flex ">
          <h1 class="text-h4 font-weight-bold text-primary mb-2 text-center">
          {{ group.name }}
          </h1>
          <v-btn v-if="isLeader" icon="mdi-delete" variant="text" color="red" class="pb-2" @click="deleteDanceGroup()"></v-btn>
        </div>
        <div class="d-flex justify-end align-center w-100">
          <div v-if="isLeader">
            <v-btn  class="bg-primary text-white" rounded elevation="4" @click="$router.push(`/create-post/${group.id}`)">
              Pievienot ierakstu
            </v-btn>
            <v-btn class="bg-accent text-white ml-4" rounded elevation="4" @click="$router.push(`/age-group-form/${group.id}`)">
              Pievienot grupu
            </v-btn>
          </div>
        </div>
      </v-col>
    </v-row>
    <v-row justify="center" class="mb-12" v-if="posts.length">
      <v-col
        v-for="post in posts"
        :key="post.id"
        cols="12"
        class="d-flex justify-center mb-12"
      >
      <v-card
        class="rounded-lg elevation-12 position-relative"
        style="overflow: hidden; width: 90%; max-width: 700px;"
      >
        <div
          v-if="!post.picture"
          class="position-absolute top-0 right-0 pa-2"
          style="z-index:2"
        >
          <v-btn  v-if="isLeader" icon="mdi-pencil" variant="text" @click="editPost(post)"></v-btn>
          <v-btn  v-if="isLeader" icon="mdi-delete" variant="text" color="red" @click="deletePost(post.id)"></v-btn>
        </div>

        <v-img
          v-if="post.picture"
          :src="post.picture"
          height="40vh"
          cover
        ></v-img>

        <v-card-actions
          v-if="post.picture"
          class="d-flex justify-end"
        >
          <v-btn  v-if="isLeader" icon="mdi-pencil" variant="text" @click="editPost(post)"></v-btn>
          <v-btn  v-if="isLeader" icon="mdi-delete" variant="text" color="red" @click="deletePost(post.id)"></v-btn>
        </v-card-actions>

        <v-card-text
          class="d-flex flex-column justify-center align-center text-center px-6 py-6"
        >
          <div class="text-subtitle-2 mb-2 text-secondary">
            {{ post.dance_group_member?.dance_group?.name || 'Nezināms kolektīvs' }}
          </div>

          <div class="text-h5 font-weight-bold mb-2">
            {{ post.title }}
          </div>

          <div class="text-body-1 mb-4">
            {{ post.description }}
          </div>

          <div class="text-caption text-secondary">
            {{ formatDate(post.created_at) }}
          </div>
        </v-card-text>
      </v-card>
      </v-col>
    </v-row>
    <div v-else class="d-flex justify-center" style="height: 100vh;">
      <span class="text-secondary text-h5">Šim kolektīvam nav ierakstu</span>
    </div>
  </v-container>
</template>

<script>
import axios from 'axios'
import { useRoute } from 'vue-router'

export default {
  name: 'GroupPosts',
  data() {
    return {
      group: {},
      posts: []
    }
  },
  setup() {
    const route = useRoute()
    return { route }
  },
  computed: {
    isLeader() {
      return this.group?.leaders?.some(
        leader => leader.user?.id === this.$root.user?.id
      ) ?? false
    }
  },
  methods: {
    async fetchPosts() {
      try {
        const groupId = this.$route.params.id
        const res = await axios.get(`/api/my-posts/${groupId}`, { withCredentials: true })
        this.group = res.data.dance_group
        this.posts = res.data.posts
      } catch (err) {
        if (err.response && err.response.status === 403) {
          this.$router.push('/no-access')
        } else {
          console.error('Kļūda ielādējot postus:', err)
          this.group = {}
          this.posts = []
        }
      }
    },
    async deletePost(postId) {
      try {
        await axios.delete(`/api/posts/${postId}`, { withCredentials: true })
        this.posts = this.posts.filter(post => post.id !== postId)
        alert('Ieraksts veiksmīgi dzēsts')
      } catch (err) {
        console.error('Kļūda dzēšot ierakstu:', err)
        alert('Neizdevās dzēst ierakstu')
      }
    },

    async deleteDanceGroup() {
      if (!confirm('Vai tiešām vēlaties dzēst šo kolektīvu? Šī darbība ir neatgriezeniska.')) {
        return
      }
      try {
        const id = this.route.params.id
        await axios.delete(`/api/danceGroups/${id}`, { withCredentials: true })
          alert('Kolektīvs veiksmīgi dzēsts')
          this.$router.push('/')
      } catch (err) {
        console.error('Kļūda dzēšot kolektīvu:', err)
        alert('Neizdevās dzēst kolektīvu')
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    }
  },
  mounted() {
    this.fetchPosts()
  },
  watch: {
    'route.params.id': function () {
      this.fetchPosts()
    }
  }
}
</script>

