<template>
  <v-app>
    <v-main>
      <v-row class="ma-0" style="height: 100vh;">

        <!-- ATVĒRTĀS UZŅEMŠANAS -->
        <v-col
          cols="12"
          md="9"
          class="pa-4 d-flex flex-column"
          style="height: 100%;"
        >
          <div class="d-flex justify-center flex-column mb-10">
            <h1 class="text-center text-accents">
              ATVĒRTĀS UZŅEMŠANAS
            </h1>

            <p class="text-subhed text-center">
              Pievienojies, kādam no Latvijas deju kolektīviem
            </p>
          </div>
          <!-- Filtri -->
          <div class="d-flex justify-end flex-wrap ga-3 mb-4 mx-auto"
              style="width: 700px; max-width: 90%;">

            <v-select
              v-model="selectedCity"
              :items="availableCities"
              label="Pilsēta"
              placeholder="Visas pilsētas"
              clearable
              variant="outlined"
              density="compact"
              hide-details
              rounded="xl"
              max-width="180"
            />

            <v-select
              v-model="selectedGenre"
              :items="availableGenres"
              item-title="title"
              item-value="value"
              label="Deju žanrs"
              placeholder="Visi žanri"
              clearable
              variant="outlined"
              density="compact"
              hide-details
              rounded="xl"
              max-width="180"
            />
          </div>
          <div class="overflow-y-auto mt-3">
            <v-card
              v-for="admission in filteredAdmissions"
              :key="admission.id"
              class=" elevation-2 mb-5 pa-4 pa-md-6 mx-auto d-flex flex-column"
              width="700"
              max-width="90%"
              rounded="lg"
            >
              <!-- Kolektīvs un poga -->
              <div class="d-flex align-center justify-space-between mb-4">
                <v-btn
                  size="small"
                  rounded="lg"
                  class="bg-accent  text-none text-small"
                  @click="openArticle(admission)"
                >
                  Pievienoties
                </v-btn>

                <div class="text-right text-small">
                  {{ admission.groupName }}
                </div>
              </div>

              <!-- Uzņemšanas informācija -->
              <div class="text-center mb-4">
                <h3 class="text-accent text-accents">
                  {{ admission.name }}
                </h3>

                <h4 class="text-small mt-2">
                  {{ admission.ageGroupName }}
                  ({{ admission.ageGroup }})
                </h4>
              </div>

              <v-divider class="mb-3" />

              <!-- Datumi -->
              <h4 class="text-small mb-4">
                {{ formatDate(admission.start_date) }} - {{ formatDate(admission.end_date) }}
              </h4>

              <!-- TODO:: Kategorijas, žanrs, pilsēta -->
              <div class="d-flex justify-end flex-wrap ga-2">
                <v-chip
                  v-if="admission"
                  class="text-accent"
                  color="transparent"
                  variant="outlined"
                  size="small"
                >
                 {{ translateGenre(admission.dance_group?.genre) }}
                </v-chip>

                <v-chip
                  class="text-accent"
                  color="transparent"
                  variant="outlined"
                  size="small"
                >
                  Iesācēji
                </v-chip>
                <v-chip
                v-if="admission"
                  class="text-accent"
                  color="transparent"
                  variant="outlined"
                  size="small"
                >
                  {{ admission.dance_group?.city  || 'Nezināma pilsēta' }} 
                </v-chip>
                <v-btn
                  icon="mdi-arrow-top-right"
                  size="small"
                  variant="text"
                  aria-label="Uzzināt vairāk"
                  @click="openArticle(admission)"
                />
              </div>
            </v-card>
            <div
              v-if="filteredAdmissions.length === 0"
              class="text-center pa-6"
            >
              Pašlaik nav pieejamu uzņemšanu pēc šadiem kritērijiem.
            </div>
          </div>
        </v-col>
      <!-- APSKATI -->
      <v-col
        cols="3"
        class="d-none d-md-block pa-4"
        style="height: 100%; overflow-y: auto;"
      >
        <h2 class="text-accent text-center text-accents">
          APSKATI!
        </h2>
        <v-list class="pa-4 mt-10">
          <v-list-item
            v-for="post in latestPosts"
            :key="post.id"
            class="border-b hover-effect mt-6 mb-6 pb-6 pt-6"
          >
            <v-list-item-title class="text-wrap text-h6">
              {{ post.title || 'Nav virsraksta' }}
            </v-list-item-title>
            <v-list-item-subtitle class="text-right mt-2 text-small">
              {{ post.dance_group_member?.dance_group?.name || 'Nav kolektīva' }}
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>
        <div class="d-flex justify-center">
          <v-btn
            rounded="lg"
            class="bg-accent mt-6 text-none text-small letter-spacing-0"
            @click="$router.push('/posts')"
          >
            Apskatīt vairāk!
          </v-btn>
        </div>
      </v-col>
      </v-row>
    </v-main>
  </v-app>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      ageGroups: [],
      posts:[],
      selectedCity: null,
      selectedGenre: null
    }
  },

  computed: {
    allAdmissions() {
      return this.ageGroups.flatMap(ageGroup =>
        (ageGroup.admissions || []).map(admission => ({
          ...admission,
          dance_group: ageGroup.dance_group,
          groupName:
            ageGroup.dance_group?.name || 'Nezināms kolektīvs',
          ageGroupName: ageGroup.name,
          ageGroup: ageGroup.age_group
        }))
      )
    },
    latestPosts() {
      if (!this.posts || this.posts.length === 0) return []
      return this.posts.slice(0, 3) // Atgriež pēdējos 3 ierakstus, ja tie eksistē
    },
    availableCities() {
      return [...new Set(
        this.allAdmissions
          .map(admission => admission.dance_group?.city)
          .filter(Boolean)
      )].sort((a, b) => a.localeCompare(b, 'lv'))
    },
    availableGenres() {
      const genres = [
        ...new Set(
          this.allAdmissions
            .map(admission => admission.dance_group?.genre)
            .filter(Boolean)
        )
      ]
      return genres.map(genre => ({
        title: this.translateGenre(genre),
        value: genre
      }))
    },
    filteredAdmissions() {
      return this.allAdmissions.filter(admission => {
        const matchesCity =
          !this.selectedCity ||
          admission.dance_group?.city === this.selectedCity
        const matchesGenre =
          !this.selectedGenre ||
          admission.dance_group?.genre === this.selectedGenre
        return matchesCity && matchesGenre
      })
    },

  },

  methods: {
    translateGenre(genre) {
      const genres = [
        { title: "Liriskās dejas", value: "lyrical dance" },
        { title: "Balets", value: "ballet" },
        { title: "Mūsdienīgās dejas", value: "contemporary dance" },
        { title: "Tautas dejas", value: "folk dance" },
        { title: "Hip-hop", value: "hip hop" },
        { title: "Cits", value: "other" }
      ]

      return genres.find(g => g.value === genre)?.title || "Nezināms žanrs"
    },
    async fetchAgeGroups() {
      try {
        const res = await axios.get('/api/admission-age-groups')
        this.ageGroups = res.data.data || []

        console.log('Ielādētās vecuma grupas ar uzņemšanām:', this.ageGroups)
      } catch (err) {
        console.error('Kļūda ielādējot uzņemšanas:', err)
        this.ageGroups = []
      }
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

    formatDate(date) {
      if (!date) return 'Datums nav norādīts'

      return new Date(date).toLocaleDateString('lv-LV')
    },

    openArticle(admission) {
      this.$router.push(`/admissions/${admission.id}`)
    }
  },

  mounted() {
    this.fetchAgeGroups()
    this.fetchPosts()
  }
}
</script>