
<template>
  <!-- Galvenais ritināmais konteiners -->
  <div class="event-scroll-container">
    <v-row justify="center" class="ma-0">
      <!-- Pasākuma informācija -->
      <v-col v-if="event" cols="12" class="pa-0">
        <v-card elevation="2" class="event-card rounded-0">
          <v-row no-gutters>
            <!-- Kreisā puse -->
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column pa-6 pa-md-8"
            >
              <div class="d-flex justify-start">
                <v-btn
                  variant="text"
                  aria-label="Atpakaļ"
                  @click="$emit('close')"
                >
                  <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
              </div>

              <div
                class="d-flex align-center justify-space-between flex-wrap ga-2 mt-4 text-small"
              >
                <h2 class="text-small">Par pasākumu</h2>

                <v-chip class="border bg-accent text-white">
                  {{ translatedGenre }}
                </v-chip>
              </div>

              <v-divider class="my-7" />

              <!-- Atrašanās vieta -->
              <div class="mb-3 d-flex flex-row align-start">
                <v-icon class="mt-1">
                  mdi-map-marker-outline
                </v-icon>

                <p class="text-para ml-3">
                  {{ event.location || 'Nav norādīts' }}
                </p>
              </div>

              <!-- Datums un laiks -->
              <div class="d-flex flex-row align-start">
                <v-icon class="mt-1">
                  mdi-clock-outline
                </v-icon>

                <p class="text-para ml-3">
                  {{ formatDate(event.date_start) }}
                  plkst. {{ formatTime(event.date_start) }}
                  -
                  {{ formatDate(event.date_end) }}
                  plkst. {{ formatTime(event.date_end) }}
                </p>
              </div>

              <v-divider class="my-6" />

              <!-- Apraksts -->
              <div class="d-flex align-center">
                <p class="text-para text-center w-100">
                  {{ event.description || 'Nav apraksta' }}
                </p>
              </div>

              <v-divider class="my-6" />
            </v-col>

            <!-- Labā puse -->
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column pa-6 pa-md-8"
            >
              <div
                class="d-flex align-end justify-space-between flex-wrap ga-2"
              >
                <h1 class="text-accent mt-6 text-uppercase text-center">
                  {{ event.name || 'Pasākuma nosaukums' }}
                </h1>

                <!-- Darbību pogas -->
                <div
                  v-if="canManageEvent"
                  class="d-flex flex-row"
                >
                  <v-btn
                    icon="mdi-pencil"
                    variant="text"
                    color="black"
                    size="small"
                    aria-label="Rediģēt pasākumu"
                    @click="openEditDialog(event)"
                  />

                  <v-btn
                    icon="mdi-delete"
                    variant="text"
                    color="error"
                    size="small"
                    aria-label="Dzēst pasākumu"
                    @click="openDeleteDialog"
                  />
                </div>
              </div>
              <!-- Veiksmīgas rediģēšanas paziņojums -->
              <v-alert
                v-if="editSuccess"
                type="success"
                variant="tonal"
                closable
                class="mt-4"
                @click:close="editSuccess = false"
              >
                Pasākums veiksmīgi atjaunināts!
              </v-alert>
              <!-- Piedalošās deju grupas -->
              <div v-if="participatingGroups.length > 0" class="mt-6">
                <!-- Poga tikai mobilajām ierīcēm -->
                <v-btn
                  class="d-md-none mb-2"
                  variant="text"
                  color="accent"
                  block
                  :aria-expanded="String(showGroups)"
                  @click="toggleGroups"
                >
                  <p class="text-small text-none">
                    Piedalās ({{ participatingGroups.length }})
                  </p>

                  <v-spacer />

                  <v-icon>
                    {{ showGroups ? 'mdi-chevron-up' : 'mdi-chevron-down' }}
                  </v-icon>
                </v-btn>
                <!-- Virsraksts datoram -->
                <h3 class="d-none d-md-block text-small mb-4">
                  Piedalās:
                </h3>
                <v-expand-transition>
                  <div
                    v-if="!isMobile || showGroups"
                    class="d-flex flex-column ga-2 group-list"
                  >
                    <v-chip
                      v-for="group in participatingGroups"
                      :key="group.id"
                      variant="outlined"
                      class="bg-white text-wrap py-4 group-chip"
                    >
                      {{
                        group.dance_group?.name ||
                        group.group?.name ||
                        group.name ||
                        'Nezināms kolektīvs'
                      }}

                      <template v-if="group.age_group">
                        ({{ group.age_group }})
                      </template>
                    </v-chip>
                  </div>
                </v-expand-transition>
              </div>
              <div v-else class="mt-6">
                <p class="text-para">
                  Neviens kolektīvs nepiedalās.
                </p>
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>

      <!-- Ielāde -->
      <v-col
        v-else-if="loading"
        cols="12"
        class="text-center py-10"
      >
        <v-progress-circular
          indeterminate
          color="primary"
          size="64"
        />
        <p class="mt-4">Ielādē pasākuma datus...</p>
      </v-col>
      <!-- Ielādes kļūda -->
      <v-col v-else cols="12" class="text-center py-10">
        <p>Neizdevās ielādēt pasākuma datus.</p>

        <v-btn class="mt-3" @click="fetchEventInfo">
          Mēģināt vēlreiz
        </v-btn>
      </v-col>
    </v-row>
  </div>
  <!-- Pasākuma rediģēšanas dialogs -->
  <v-dialog
    v-model="editDialog"
    max-width="700"
    persistent
  >
    <v-card rounded="lg">
      <h3
        class="text-h6 text-accent text-accents text-small text-center pa-6"
      >
        REDIĢĒT PASĀKUMU
      </h3>

      <v-card-text class="pa-5">
        <v-form
          ref="editForm"
          @submit.prevent="updateEvent"
        >
          <v-text-field
            v-model="editFormData.name"
            label="Pasākuma nosaukums"
            variant="outlined"
            :rules="[
              v => !!v?.trim() || 'Nosaukums ir obligāts'
            ]"
            required
          />

          <v-text-field
            v-model="editFormData.location"
            label="Atrašanās vieta"
            variant="outlined"
            :rules="[
              v => !!v?.trim() || 'Atrašanās vieta ir obligāta'
            ]"
            required
          />

          <v-textarea
            v-model="editFormData.description"
            label="Pasākuma apraksts"
            variant="outlined"
            rows="4"
          />

          <v-text-field
            v-model="editFormData.date_start"
            label="Sākuma datums un laiks"
            type="datetime-local"
            variant="outlined"
            :rules="[
              v => !!v || 'Norādi sākuma laiku'
            ]"
            required
          />

          <v-text-field
            v-model="editFormData.date_end"
            label="Beigu datums un laiks"
            type="datetime-local"
            variant="outlined"
            :rules="[
              v => !!v || 'Norādi beigu laiku'
            ]"
            required
          />

          <v-alert
            v-if="editError"
            type="error"
            variant="tonal"
            closable
            class="mb-4"
            @click:close="editError = ''"
          >
            {{ editError }}
          </v-alert>

          <div class="d-flex justify-end ga-3 mt-4">
            <v-btn
              variant="outlined"
              class="text-none text-small"
              :disabled="savingEdit"
              @click="closeEditDialog"
            >
              Atcelt
            </v-btn>

            <v-btn
              type="submit"
              color="accent"
              class="text-none text-small"
              :loading="savingEdit"
              :disabled="savingEdit"
            >
              Saglabāt
              <v-icon end>mdi-check</v-icon>
            </v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-dialog>

  <!-- Pasākuma dzēšanas apstiprinājums -->
  <v-dialog
    v-model="deleteEventDialog"
    max-width="500"
    persistent
  >
    <v-card class="pa-4">
      <v-card-title class="text-h6 text-center">
        Dzēst pasākumu?
      </v-card-title>

      <v-card-text class="text-center text-para">
        Vai tiešām vēlies dzēst pasākumu
        <strong>{{ event?.name }}</strong>?
        Šo darbību nevarēs atsaukt.
      </v-card-text>

      <v-alert
        v-if="deleteError"
        type="error"
        variant="tonal"
        class="mb-4"
      >
        {{ deleteError }}
      </v-alert>

      <v-card-actions class="justify-center ga-3">
        <v-btn
          variant="outlined"
          :disabled="deletingEvent"
          @click="closeDeleteDialog"
        >
          Nē, atcelt
        </v-btn>

        <v-btn
          color="error"
          :loading="deletingEvent"
          :disabled="deletingEvent"
          @click="deleteEvent"
        >
          Jā, dzēst
          <v-icon end>mdi-delete</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

const genreMap = {
  'lyrical dance': 'Liriskā deja',
  'contemporary dance': 'Mūsdienīgās dejas',
  ballet: 'Balets',
  'hip hop': 'Hip-hops',
  'folk dance': 'Tautas dejas',
  other: 'Cits',
}

export default {
  name: 'ShowEvent',

  props: {
    eventId: {
      type: [Number, String],
      required: true,
    },
  },

  emits: ['close'],

  data() {
    return {
      event: null,
      loading: true,

      // Kolektīvu saraksts
      showGroups: false,
      isMobile: false,
      resizeHandler: null,

      // Rediģēšana
      editDialog: false,
      savingEdit: false,
      editError: '',
      editSuccess: false,

      editFormData: {
        name: '',
        location: '',
        description: '',
        date_start: '',
        date_end: '',
      },

      // Dzēšana
      deleteEventDialog: false,
      deletingEvent: false,
      deleteError: '',
    }
  },

  computed: {
    authStore() {
      return useAuthStore()
    },

    participatingGroups() {
      const groups = this.event?.dance_groups
      return Array.isArray(groups) ? groups : []
    },

    translatedGenre() {
      const genre = this.event?.dance_group_member?.genre

      if (!genre) {
        return 'Nav norādīts'
      }

      return genreMap[genre.toLowerCase()] || genre
    },

    canManageEvent() {
      if (this.authStore.isAdmin) {
        return true
      }

      const creatorGroupId =
        this.event?.dance_group_member?.dance_group_id

      if (!creatorGroupId) {
        return false
      }

      return this.authStore.leaderGroups.some(
        group => Number(group.id) === Number(creatorGroupId)
      )
    },
  },

  watch: {
    eventId: {
      immediate: true,

      handler() {
        this.showGroups = false
        this.fetchEventInfo()
      },
    },

    isMobile(value) {
      if (!value) {
        this.showGroups = false
      }
    },
  },

  mounted() {
    this.updateScreenSize()

    this.resizeHandler = () => {
      this.updateScreenSize()
    }

    window.addEventListener('resize', this.resizeHandler)
  },

  beforeUnmount() {
    if (this.resizeHandler) {
      window.removeEventListener('resize', this.resizeHandler)
    }
  },

  methods: {
    updateScreenSize() {
      this.isMobile = window.innerWidth < 960
    },

    // Atver un aizver grupu sarakstu.
    toggleGroups() {
      this.showGroups = !this.showGroups
    },

    // Ielādē pasākuma informāciju.
    async fetchEventInfo() {
      if (!this.eventId) {
        this.event = null
        this.loading = false
        return
      }

      this.loading = true
      this.event = null

      try {
        const response = await axios.get(
          `/api/events/${this.eventId}`,
          {
            withCredentials: true,
            timeout: 15000,
          }
        )

        this.event = response.data?.data ?? response.data
      } catch (error) {
        console.error(
          'Pasākuma ielādes kļūda:',
          error.response?.data || error
        )

        this.event = null
      } finally {
        this.loading = false
      }
    },

    formatDate(value) {
      if (!value) {
        return 'Nav norādīts'
      }

      const date = new Date(value.replace(' ', 'T'))

      if (Number.isNaN(date.getTime())) {
        return 'Nav norādīts'
      }

      return date.toLocaleDateString('lv-LV', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
      })
    },

    formatTime(value) {
      if (!value) {
        return 'Nav norādīts'
      }

      const date = new Date(value.replace(' ', 'T'))

      if (Number.isNaN(date.getTime())) {
        return 'Nav norādīts'
      }

      return date.toLocaleTimeString('lv-LV', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
      })
    },

    // Atver rediģēšanas dialogu.
    openEditDialog(event) {
      this.editError = ''
      this.editSuccess = false

      this.editFormData = {
        name: event.name || '',
        location: event.location || '',
        description: event.description || '',
        date_start: this.toDateTimeLocal(event.date_start),
        date_end: this.toDateTimeLocal(event.date_end),
      }

      this.editDialog = true
    },

    toDateTimeLocal(value) {
      if (!value) {
        return ''
      }

      return value.replace(' ', 'T').slice(0, 16)
    },

    closeEditDialog() {
      if (this.savingEdit) {
        return
      }

      this.editDialog = false
      this.editError = ''
    },

    // Saglabā pasākuma izmaiņas.
    async updateEvent() {
      if (!this.event?.id || this.savingEdit) {
        return
      }

      const { valid } = await this.$refs.editForm.validate()

      if (!valid) {
        return
      }

      const start = new Date(this.editFormData.date_start)
      const end = new Date(this.editFormData.date_end)

      if (
        Number.isNaN(start.getTime()) ||
        Number.isNaN(end.getTime()) ||
        end <= start
      ) {
        this.editError =
          'Beigu datumam un laikam jābūt pēc sākuma datuma un laika.'
        return
      }

      this.savingEdit = true
      this.editError = ''
      this.editSuccess = false

      try {
        const payload = {
          name: this.editFormData.name.trim(),
          location: this.editFormData.location.trim(),
          description: this.editFormData.description || '',
          date_start:
            `${this.editFormData.date_start.replace('T', ' ')}:00`,
          date_end:
            `${this.editFormData.date_end.replace('T', ' ')}:00`,
        }

        const response = await axios.put(
          `/api/event/${this.event.id}`,
          payload,
          { withCredentials: true }
        )

        const updatedEvent =
          response.data?.data ?? response.data

        this.event = {
          ...this.event,
          ...updatedEvent,
        }

        this.editDialog = false
        this.editSuccess = true
      } catch (error) {
        console.error(
          'Pasākuma atjaunināšanas kļūda:',
          error.response?.data || error
        )

        const validationErrors =
          error.response?.data?.errors || {}

        this.editError =
          Object.values(validationErrors).flat().join(' ') ||
          error.response?.data?.message ||
          'Neizdevās atjaunināt pasākumu. Mēģini vēlreiz.'
      } finally {
        this.savingEdit = false
      }
    },

    // Atver dzēšanas apstiprinājumu.
    openDeleteDialog() {
      this.deleteError = ''
      this.deleteEventDialog = true
    },

    closeDeleteDialog() {
      if (this.deletingEvent) {
        return
      }

      this.deleteEventDialog = false
      this.deleteError = ''
    },

    // Dzēš pašreizējo pasākumu.
    async deleteEvent() {
      if (!this.event?.id || this.deletingEvent) {
        return
      }

      this.deletingEvent = true
      this.deleteError = ''

      try {
        await axios.delete(
          `/api/events/${this.event.id}`,
          { withCredentials: true }
        )

        this.deleteEventDialog = false
        this.$emit('close')
      } catch (error) {
        console.error(
          'Pasākuma dzēšanas kļūda:',
          error.response?.data || error
        )

        this.deleteError =
          error.response?.data?.message ||
          'Neizdevās izdzēst pasākumu. Mēģini vēlreiz.'
      } finally {
        this.deletingEvent = false
      }
    },
  },
}
</script>

<style scoped>
.event-scroll-container {
  width: 100%;
  max-height: 90vh;
  max-height: 90dvh;
  overflow-y: auto;
  overflow-x: hidden;
  overscroll-behavior-y: contain;
  -webkit-overflow-scrolling: touch;
}

.event-card {
  min-height: min-content;
}

.group-list {
  padding: 4px;
}

.group-chip {
  border-color: #02317a;
  min-height: 56px;
  height: auto;
  white-space: normal;
}

@media (max-width: 959px) {
  .event-scroll-container {
    max-height: 85vh;
    max-height: 85dvh;
  }
}
</style>

