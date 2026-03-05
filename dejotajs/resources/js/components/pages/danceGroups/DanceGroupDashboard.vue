<template>
  <h1 class="text-center mt-8">Deju kolektīvi</h1>

  <v-container class="mb-4">
    <v-row align="center" justify="space-between" class="mx-auto" style="max-width: 700px;">
      <!-- Meklēšanas lauks kreisajā pusē -->
      <v-col cols="12" md="6">
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

      <!-- Sortēšanas un filtrēšanas pogu grupa labajā pusē -->
      <v-col cols="12" md="6" class="d-flex justify-end">
        <!-- Sortēšana -->
        <v-btn
          icon
          color="primary"
          @click="sortGroups"
          :title="sortOrder === 'asc' ? 'Kārtot augošā secībā' : 'Kārtot dilstošā secībā'"
          class="me-2"
        >
          <v-icon small>{{ sortOrder === 'asc' ? 'mdi-arrow-up' : 'mdi-arrow-down' }}</v-icon>
        </v-btn>

        <!-- Filtrēšana -->
        <v-menu v-model="filterMenu" transition="scale-transition" offset-y :close-on-content-click="false">
          <template #activator="{ props }">
            <v-btn icon color="primary" v-bind="props">
              <v-icon small>mdi-filter-variant</v-icon>
            </v-btn>
          </template>

          <v-card style="min-width: 250px; max-width: 350px;">
            <v-card-title>Filtrēt kolektīvus</v-card-title>
            <v-card-text>
              <v-select
                v-model="selectedCity"
                :items="cities"
                label="Pilsēta"
                clearable
                dense
                outlined
              ></v-select>

              <v-select
                v-model="selectedGenre"
                :items="genres"
                label="Žanrs"
                clearable
                dense
                outlined
                class="mt-2"
              ></v-select>
            </v-card-text>
            <v-card-actions>
              <v-btn color="primary" text @click="applyFilters">Filtrēt</v-btn>
              <v-btn color="secondary" text @click="resetFilters">Atcelt</v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>
      </v-col>
    </v-row>
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
      cities: [],
      genres: [],
      selectedCity: null,
      selectedGenre: null,
      searchQuery: '',
      sortOrder: 'asc',
      filterMenu: false,
    }
  },
  methods: {
    async fetchFilterOptions() {
      const res = await axios.get('/filter-options')
      this.cities = res.data.cities || []
      this.ageGroups = res.data.age_groups || []
      this.genres = res.data.genres || []
    },
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
    },
    async fetchGroups() {
      const res = await axios.get('/filter-dance-groups', {
        params: {
          city: this.selectedCity,
          age_group: this.selectedAgeGroup,
          genre: this.selectedGenre,
          q: this.searchQuery,
          sort: this.sortOrder
        }
      })

      this.groups = res.data.data || []
    },
    applyFilters() {
      this.fetchGroups()
      this.filterMenu = false
    },
    sortGroups() {
      if (this.sortOrder === 'asc') {
        this.sortOrder = 'desc'
      } else {
        this.sortOrder = 'asc'
      }

      axios
        .get(`/sort-dance-groups/${this.sortOrder}`)
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : []
        })
        .catch(err => console.error(err))
    },
    resetFilters() {
      this.selectedCity = null
      this.selectedGenre = null
      this.fetchGroups()
      this.filterMenu = false
    }
  },
  mounted() {
    this.fetchFilterOptions(),
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