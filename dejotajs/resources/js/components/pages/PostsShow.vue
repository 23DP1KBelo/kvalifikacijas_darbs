<template>
  <v-container fluid class="pa-0">
    <v-row class="d-flex justify-center" no-gutters>
      <v-col cols="12" v-for="post in posts" :key="post.id" class="d-flex justify-center">
        <v-card
          class="mx-4 my-6"
          elevation="16"
          color="softblue"
          width="600"
        >
          <!-- Attēls augšā -->
          <v-img
            v-if="post.picture"
            :src="post.picture"
            height="350px"
            cover
          ></v-img>

          <!-- Teksts zem attēla vai kartīte, ja nav bilde -->
          <v-card-text class="px-4 py-4">
            <v-card-title class="text-h5 font-weight-bold">{{ post.title }}</v-card-title>
            <v-card-subtitle class="mb-2">{{ post.dance_group_member?.dance_group?.name || 'Nezināms kolektīvs' }}</v-card-subtitle>
            <v-card-text class="text-body-1">{{ post.description || ' ' }}</v-card-text>
          </v-card-text>

          <!-- Datums -->
          <v-card-subtitle class="d-flex justify-end px-4 pb-4 text-caption">
            {{ formatDate(post.created_at) }}
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const posts = ref([])

onMounted(async () => {
  try {
    const response = await fetch('/api/posts')
    const data = await response.json()
    console.log('Iegūtie postu dati:', data)
    posts.value = Array.isArray(data.data) 
      ? data.data.map(post => ({
          ...post,
          picture: post.picture ? post.picture : null
        }))
      : []
  } catch (err) {
    console.error('Kļūda ielādējot postus:', err)
    posts.value = []
  }
})

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('lv-LV', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>

<style>
.fill-height {
  min-height: 100vh;
}
</style>