<template>
  <v-container>
    <h1 class="text-center mt-8">{{ group.name }}</h1>
    <v-card v-for="post in posts" :key="post.id" class="mx-auto my-8 pb-7" color="secondary" elevation="16"  >
      <v-card-subtitle class="mt-6">{{ post.dance_group_member?.dance_group?.name}}</v-card-subtitle>
      <v-card-title class="text-h4">{{ post.title }}</v-card-title>
      <v-card-text class="text-body-1">{{ post.description }}</v-card-text>
      <br>
      <v-card-subtitle class="d-flex justify-end text-subtitle-2">{{ new Date(post.created_at).toLocaleDateString()  }}</v-card-subtitle>
    </v-card>

    <div v-if="posts.length === 0" class="text-center mt-8">
      Šim kolektīvam nav ierakstu
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
  methods: {
    async fetchPosts() {
      try {
        const groupId = this.route.params.id
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
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    }
  },
  mounted() {
    this.fetchPosts()
  },
  watch: {
    // kad route param id mainās, fetchē jaunus postus
    'route.params.id': function () {
      this.fetchPosts()
    }
  }
}
</script>


