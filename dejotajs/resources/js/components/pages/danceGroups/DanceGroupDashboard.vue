<template>
  <v-container class="py-8">
    <div class="text-center mb-8">
      <h1 class="page-title">Deju kolektīvi</h1>
      <p class="page-subtitle">
        Atrodi deju kolektīvus Latvijā
      </p>
    </div>
    <!-- Filtrēšana un meklēšana -->
    <v-card class="pa-4 mb-6 search-card" elevation="2">
      <v-row align="center" justify="space-between">

        <v-col cols="12" md="6">
          <v-text-field v-model="searchQuery" label="Meklēt kolektīvu" prepend-inner-icon="mdi-magnify" learable variant="outlined" density="comfortable" hide-details @input="searchGroups"/>
        </v-col>

        <v-col cols="12" md="6" class="d-flex justify-end">

          <!-- kārtošana -->
          <v-btn icon variant="tonal" color="text" @click="sortGroups" class="me-2">
            <v-icon>
              {{ sortOrder === 'asc' ? 'mdi-sort-alphabetical-ascending' : 'mdi-sort-alphabetical-descending' }}
            </v-icon>
          </v-btn>
          <!-- filtru menu -->
          <v-menu v-model="filterMenu" transition="scale-transition" offset-y :close-on-content-click="false">
            <template #activator="{ props }">
              <v-btn icon variant="tonal" color="text" v-bind="props">
                <v-icon>mdi-filter-variant</v-icon>
              </v-btn>
            </template>
            <v-card class="pa-2" width="320">
              <v-card-title class="text-h6">
                Filtrēt kolektīvus
              </v-card-title>
              <v-card-text>
                <v-select
                  v-model="selectedCity"
                  :items="cities"
                  label="Pilsēta"
                  clearable
                  variant="outlined"
                  density="comfortable"
                  class="mb-3"
                />
                <v-select
                  label="Žanrs"
                  :items="genres"
                  item-title="title"
                  item-value="value"
                  v-model="selectedGenre"
                  variant="outlined"
                  density="comfortable"
                />
              </v-card-text>
              <v-card-actions>
                <v-spacer />
                <v-btn variant="tonal" color="text" @click="applyFilters">
                  Filtrēt
                </v-btn>
                <v-btn variant="secondary" @click="resetFilters">
                  Atcelt
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>
        </v-col>
      </v-row>
    </v-card>
    <!-- Ja nav kolektīvu, alert -->
    <v-alert
      v-if="!groups.length"
      type="info"
      variant="tonal"
      border="start"
      icon="mdi-information-outline"
      class="mb-6"
    >
      Nav atrasts neviens kolektīvs pēc izvēlētajiem filtriem.
    </v-alert>
    <!-- GROUP CARDS -->
    <v-row>
      <v-col
        v-for="group in groups"
        :key="group.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <v-card
          elevation="3"
          class="group-card bg-softblue"
          @click="goTo(`/group-info/${group.id}`)"
        >
          <v-img
            v-if="group.picture_url"
            :src="group.picture_url"
            height="200"
            cover
          />
          <v-card-title class="group-title">
            {{ group.name }}
          </v-card-title>
          <v-card-subtitle class="text-truncate">
            {{
              group.leaders?.length
                ? group.leaders.map(l => `${l.user?.name || ''} ${l.user?.surname || ''}`).join(', ')
                : 'Nav vadītāju'
            }}
          </v-card-subtitle>
          <v-card-text class="text-medium-emphasis">
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
      selectedCity: null,
      selectedGenre: null,
      searchQuery: '',
      sortOrder: 'asc',
      filterMenu: false,
      genres: [
          { title: "Liriskās dejas", value: "lyrical dance" },
          { title: "Balets", value: "ballet" },
          { title: "Mūsdienīgās dejas", value: "contemporary dance" },
          { title: "Tautas dejas", value: "folk dance" },
          { title: "Hip-hop", value: "hip hop" },
          { title: "Cits", value: "other" }
      ]
    }
  },
  methods: {
    async fetchFilterOptions() {
      const res = await axios.get('/filter-options')
      this.cities = res.data.cities || []
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

.page-title{
  font-size: 34px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.page-subtitle{
  opacity: 0.7;
  font-size: 15px;
}

.search-card{
  border-radius:16px;
}

.group-card{
  border-radius:18px;
  cursor:pointer;
  transition:all .25s ease;
  overflow:hidden;
}

.group-card:hover{
  transform:translateY(-6px);
  box-shadow:0 15px 35px rgba(0,0,0,0.15);
}

.group-title{
  font-weight:600;
  font-size:18px;
}

.text-truncate{
  white-space:nowrap;
  overflow:hidden;
  text-overflow:ellipsis;
}

</style>