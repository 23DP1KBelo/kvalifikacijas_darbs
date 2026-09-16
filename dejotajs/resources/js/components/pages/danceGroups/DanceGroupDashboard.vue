<template>
  <v-container fluid class="py-10">

    <!-- Virsraksts -->
    <div class="d-flex justify-center flex-column mb-10">
      <h1 class="text-center text-accents">
        DEJU KOLEKTĪVI
      </h1>

      <p class="text-subhed text-center">
        Atrodi savu deju kolektīvu Latvijā!
      </p>
    </div>

    <!-- Meklēšana un filtri -->
    <v-container>
      <v-card
        class="pa-4 mb-8 rounded-0 elevation-2"
      >
        <v-row class="align-center">

          <!-- Meklēšana -->
          <v-col
            cols="12"
            md="7"
          >
            <v-text-field
              v-model="searchQuery"
              label="Meklēt kolektīvu"
              prepend-inner-icon="mdi-magnify"
              clearable
              variant="outlined"
              density="comfortable"
              hide-details
              @input="searchGroups"
              class="text-text"
            />
          </v-col>

          <!-- Darbības -->
          <v-col
            cols="12"
            md="5"
            class="d-flex justify-end"
          >

            <!-- Kārtošana -->
            <v-btn
              icon
              variant="tonal"
              color="text"
              @click="sortGroups"
              class="me-2"
              aria-label="Kārtot pēc nosaukuma"
            >
              <v-icon>
                {{
                  sortOrder === 'asc'
                    ? 'mdi-sort-alphabetical-ascending'
                    : 'mdi-sort-alphabetical-descending'
                }}
              </v-icon>
            </v-btn>

            <!-- Filtri -->
            <v-menu
              v-model="filterMenu"
              transition="scale-transition"
              offset-y
              :close-on-content-click="false"
            >
              <template #activator="{ props }">
                <v-btn
                  icon
                  variant="tonal"
                  color="text"
                  v-bind="props"
                  aria-label="Filtri"
                >
                  <v-icon>
                    mdi-filter-variant
                  </v-icon>
                </v-btn>
              </template>

              <v-card
                class="pa-2 rounded-0"
                width="320"
              >
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
                    v-model="selectedGenre"
                    label="Žanrs"
                    :items="genres"
                    item-title="title"
                    item-value="value"
                    clearable
                    variant="outlined"
                    density="comfortable"
                  />

                </v-card-text>

                <v-card-actions>
                  <v-spacer />

                  <v-btn
                    variant="tonal"
                    color="text"
                    @click="applyFilters"
                  >
                    Filtrēt
                  </v-btn>

                  <v-btn
                    variant="tonal"
                    @click="resetFilters"
                  >
                    Atcelt
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-menu>

          </v-col>
        </v-row>
      </v-card>

      <!-- Nav rezultātu -->
      <v-alert
        v-if="!groups.length"
        type="info"
        variant="tonal"
        icon="mdi-information-outline"
        class="mb-8"
      >
        Nav atrasts neviens kolektīvs pēc izvēlētajiem filtriem.
      </v-alert>

      <!-- Kolektīvu kartītes -->
      <v-row>
        <v-col
          v-for="group in groups"
          :key="group.id"
          cols="12"
          sm="6"
          lg="3"
          class="d-flex"
        >
          <v-card
            class="w-100 d-flex flex-column elevation-3 rounded-0 card-hover"
            height="350"
            @click="goTo(`/group-info/${group.id}`)"
          >

            <!-- Kolektīva nosaukums -->
            <h2
              class="font-weight-bold text-left px-4 pt-10 text-accent"
            >
              {{ group.name }}
            </h2>

            <!-- Vadītāji -->
            <div class="px-4 pt-1">
              <div class="text-caption text-medium-emphasis mb-1">
                <v-icon size="16" class="mr-1">
                  mdi-account-outline
                </v-icon>
                Vadītāji
              </div>

              <div class="text-body-2 text-truncate">
                {{
                  group.leaders?.length
                    ? group.leaders
                        .map(
                          l =>
                            `${l.user?.name || ''} ${l.user?.surname || ''}`
                        )
                        .join(', ')
                    : 'Nav vadītāju'
                }}
              </div>
            </div>

            <!-- Adrese -->
            <div class="px-4 pt-4">
              <div class="text-caption text-medium-emphasis mb-1">
                <v-icon size="16" class="mr-1">
                  mdi-map-marker-outline
                </v-icon>
                Atrašanās vieta
              </div>

              <div class="text-body-2 text-truncate">
                {{ group.city }}
                <span v-if="group.address">
                  · {{ group.address }}
                </span>
              </div>
            </div>

            <!-- Žanrs -->
            <div class="px-4 pt-4">
              <div class="text-caption text-medium-emphasis mb-1">
                <v-icon size="16" class="mr-1">
                  mdi-dance-ballroom
                </v-icon>
                Žanrs
              </div>

              <div class="text-body-2 text-truncate">
                <span v-if="group.genre">
                  {{ getGenreTitle(group.genre) }}
                </span>
              </div>
            </div>

            <v-spacer />

            <!-- Apakšējā daļa -->
            <v-card-actions
              class="d-flex justify-end px-4 pb-4"
            >
              <v-btn
                icon="mdi-arrow-top-right"
                variant="text"
                aria-label="Skatīt kolektīvu"
                @click.stop="goTo(`/group-info/${group.id}`)"
              />
            </v-card-actions>

          </v-card>

        </v-col>
      </v-row>

    </v-container>
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
      const res = await axios.get('/api/filter-options/')
      this.cities = res.data.cities || []
    },
    fetchDanceGroups() {
      axios
        .get('/api/danceGroups-all')
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : []
        })
        .catch(err => console.log(err))
    },
    goTo(route) {
      this.$router.push(route)
    },
    getGenreTitle(genre) {
      const foundGenre = this.genres.find(
        item => item.value === genre
      )

      return foundGenre?.title || genre
    },
    searchGroups() {
      axios
        .get('/api/search-dance-groups', { params: { q: this.searchQuery } })
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : []
        })
        .catch(err => console.error(err))
    },
    async fetchGroups() {
      const res = await axios.get('/api/filter-dance-groups', {
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
        .get(`/api/sort-dance-groups/${this.sortOrder}`)
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