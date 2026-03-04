<template>
  <h1 class="text-center mt-8">Deju kolektīvi</h1>

  <v-container class="d-flex justify-center mb-4">
    <v-col cols="12" sm="6" md="4">
      <v-text-field
        v-model="searchQuery"
        label="Meklēt kolektīvu"
        prepend-inner-icon="mdi-magnify"
        clearable
        dense
        outlined
        @input="searchGroups"
      ></v-text-field>
    </v-col>
  </v-container>

  <v-container fluid>
    <v-row class="mx-8" >
      <v-col v-for="group in groups" :key="group.id" cols="12" sm="6" md="4" lg="3">
        <v-card elevation="3" class="group-card" color="accent" dark  @click="goTo(`/group-info/${group.id}`)">
          <v-img v-if="group.picture_url" :src="group.picture_url" class="card-image" height="200px" cover/>
          <v-card-title class="title mb-1">
            {{ group.name }}
          </v-card-title>

          <v-card-subtitle class="text-truncate mb-1">
            {{ group.leaders?.length
              ? group.leaders.map(l => `${l.user?.name || ''} ${l.user?.surname || ''}`).join(', ')
              : 'Nav vadītāju' }}
          </v-card-subtitle>

          <v-card-text class="text-truncate">
            {{ group.city }}, {{ group.address }}
          </v-card-text>
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
      groups: [],
      searchQuery: ''
    }
  },
  methods: {
    fetchDanceGroups() {
      axios
        .get('/danceGroups-all')
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : []
        })
        .catch(err => console.log(err))
    },
    goTo(route) {
      this.$router.push(route)
    },
    searchGroups() {
      axios
        .get('/search-dance-groups', { params: { q: this.searchQuery } })
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : []
        })
        .catch(err => console.error(err))
    }
  },
  mounted() {
    this.fetchDanceGroups()
  }
}
</script>

<style scoped>
.group-card {
  display: flex;
  flex-direction: column;
  border-radius: 16px;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.group-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
}

.card-image {
  width: 100%;
  object-fit: cover;
}

.title {
  font-weight: 600;
}

.text-truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>