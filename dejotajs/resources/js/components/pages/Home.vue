<template>
    <v-container>
      <h1 class="text-center mt-8">Aktualitātes</h1>
        <v-card v-for="post in posts" :key="post.id" class="mx-auto my-8 pb-7" color="secondary" elevation="16"  >
          <v-card-title class="text-h4">{{ post.title }}</v-card-title>
          <v-card-subtitle>{{ post.dance_group_member?.dance_group?.name}}</v-card-subtitle>
          <v-card-text class="text-body-1">{{ post.description }}</v-card-text>
          <br>
          <v-card-subtitle class="d-flex justify-end text-subtitle-2">{{ new Date(post.created_at).toLocaleDateString()  }}</v-card-subtitle>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'

  const posts = ref(null)

  onMounted(async () => {
    const response = await fetch('/api/posts')
    const data = await response.json()
    posts.value = data.data
  })

</script>
