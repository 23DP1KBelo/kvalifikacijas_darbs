<template>
  <v-container class="pa-4">
      <h1 class="text-center text-accents">
        KALENDĀRS
      </h1>
      <p class="text-subhed text-center mb-6">
        Vieta, kur satiekas deja un notikumi
      </p>
    <div class="mb-2 d-flex flex-row justify-end">
      <v-btn
        v-if="!loadingProfile && isLeader"
        color="accent"
        rounded="xl"
        prepend-icon="mdi-plus"
        class="text-none text-small"
        @click="$router.push('/calender/event')"
      >
        Pievienot pasākumu
      </v-btn>
    </div>
    <v-card class="mx-auto my-4" max-width="1200" outlined>
      <v-card-text>
        <v-row dense align="center">
          <!-- Visi pasākumi -->
          <v-col cols="auto">
            <v-chip
              :color="selectedGenre === null ? 'primary' : undefined"
              :variant="selectedGenre === null ? 'flat' : 'outlined'"
              pill
              class="text-small"
              @click="filterEvents(null)"
            >
              Visi
            </v-chip>
          </v-col>
          <!-- Žanru filtri -->
          <v-col
            v-for="(color, genre) in genreColorsLV"
            :key="genre"
            cols="auto"
          >
            <v-chip
              :color="color"
              :variant="selectedGenre === genre ? 'flat' : 'outlined'"
              :style="{
                backgroundColor: selectedGenre === genre ? color : 'transparent',
                color: selectedGenre === genre ? '#fff' : color
              }"
              pill
              class="text-small"
              @click="filterEvents(genre)"
            >
              {{ genre }}
            </v-chip>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <v-card class="mx-auto calendar-card" max-width="1200">
      <v-card-title class="d-flex align-center justify-space-between">
        <v-btn icon="mdi-chevron-left" variant="text" @click="prev" />
        <div class="text-h6 font-weight-medium">{{ monthTitle }}</div>
        <v-btn icon="mdi-chevron-right" variant="text" @click="next" />
      </v-card-title>
      <v-divider />
      <v-card-text>
        <full-calendar
          ref="fullCalendar"
          :options="calendarOptions"
          class="modern-calendar"
        />
      </v-card-text>
    </v-card>
    <v-dialog
      v-model="dialog"
      max-width="1100"
      width="calc(100% - 24px)"
    >
      <ShowEvent
        v-if="selectedEvent"
        :event-id="selectedEvent.id"
        @close="dialog = false"
      />
    </v-dialog>
  </v-container>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import lvLocale from '@fullcalendar/core/locales/lv'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import ShowEvent from '../calender/ShowEvent.vue';

export default {
  components: { 
    FullCalendar,
    ShowEvent
  },

  data() {
    return {
      events: [],
      isEventCreator: false,
      dialog: false,
      editDialog: false,
      selectedEvent: null,
      selectedGenre: null,

      editForm: {
        id: null,
        name: '',
        location: '',
        description: '',
        date_start: '',
        date_end: '',
        dance_groups: []
      },

      genreColorsLV: {
        'Liriskā deja': '#777FC4',
        'Balets': '#4776B8',
        'Mūsdienu deja': '#368FA8',
        'Tautas deja': '#5B9BCB',
        'Hip-hops': '#345A91',
        'Cits': '#899BB8'
      },
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: lvLocale,
        initialView: 'dayGridMonth',
        headerToolbar: {
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
          today: 'Šodien',
          month: 'Mēnesis',
          week: 'Nedēļa',
          day: 'Diena'
        },
        events: [],
        eventClick: (info) => this.openEventDialog(info.event)
      }
    }
  },

  computed: {
    authStore() {
      return useAuthStore()
    },

    isLeader() {
      return this.authStore.isLeader
    },

    isAdmin() {
      return this.authStore.isAdmin
    },

    loadingProfile() {
      return this.authStore.loading
    },

    danceGroups() {
      return this.authStore.leaderGroups
    },

    monthTitle() {
      const calendarApi = this.$refs.fullCalendar?.getApi()
      return calendarApi ? calendarApi.view.title : ''
    }
  },

  methods: {
    async fetchEvents(genre = null) {
      try {
        const url = genre
          ? `/api/events/filter/${encodeURIComponent(genre)}`
          : '/api/events'

        const res = await axios.get(url)

        this.events = res.data.data || []

        const genreColors = {
          'lyrical dance': '#777FC4',
          'ballet': '#4776B8',
          'contemporary dance': '#368FA8',
          'folk dance': '#5B9BCB',
          'hip hop': '#345A91',
          'other': '#899BB8'
        }
        this.calendarOptions.events = this.events.map(e => {
          const genre = e.dance_group_member?.genre
          const color = genreColors[genre] || '#808080'

          return {
            id: e.id,
            title: e.name,
            start: e.date_start,
            end: e.date_end || e.date_start,
            backgroundColor: color,
            borderColor: color,
            extendedProps: {
              location: e.location,
              description: e.description,
              dance_groups: e.dance_groups,
              dance_group_member: e.dance_group_member
            }
          }
        })
      } catch (err) {
        console.error('Neizdevās ielādēt pasākumus:', err)
      }
    },

    async filterEvents(genre) {
      this.selectedGenre = genre

      const genreApiValues = {
        'Liriskā deja': 'lyrical dance',
        'Balets': 'ballet',
        'Mūsdienu deja': 'contemporary dance',
        'Tautas deja': 'folk dance',
        'Hip-hops': 'hip hop',
        'Cits': 'other'
      }

      const apiGenre = genre ? genreApiValues[genre] : null

      await this.fetchEvents(apiGenre)
    },

    genreLV(genre) {
      const map = {
        'lyrical dance': 'Liriskā deja',
        'ballet': 'Balets',
        'contemporary dance': 'Mūsdienu deja',
        'folk dance': 'Tautas deja',
        'hip hop': 'Hip-hops',
        'other': 'Cits'
      }

      return map[genre] || genre || 'Nezināms žanrs'
    },

    openEventDialog(event) {
      this.selectedEvent = event
      this.dialog = true
      this.eventeditMember()
    },

    formatDate(date) {
      if (!date) return '—'

      return new Date(date).toLocaleString('lv-LV', {
        dateStyle: 'short',
        timeStyle: 'short'
      })
    },

    prev() {
      this.$refs.fullCalendar?.getApi().prev()
    },

    next() {
      this.$refs.fullCalendar?.getApi().next()
    },

    eventeditMember() {
      const eventMemberId =
        this.selectedEvent?.extendedProps?.dance_group_member?.id

      this.isEventCreator = this.authStore.danceGroupMembers.some(member =>
        member.member_id === eventMemberId &&
        member.role === 'leader' &&
        member.status === 'approved'
      )
    },

    editEvent() {
      const e = this.selectedEvent
      if (!e) return

      this.editForm = {
        id: e.id,
        name: e.title,
        location: e.extendedProps.location || '',
        description: e.extendedProps.description || '',
        date_start: this.formatForInput(e.start),
        date_end: this.formatForInput(e.end),
        dance_groups: e.extendedProps.dance_groups || []
      }

      this.editDialog = true
    },

    formatForInput(date) {
      if (!date) return ''

      const d = new Date(date)
      const pad = n => n.toString().padStart(2, '0')

      return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
    },

    formatForDB(date) {
      if (!date) return null

      const d = new Date(date)
      const pad = n => n.toString().padStart(2, '0')

      return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`
    },

    async submitEditEvent() {
      try {
        const payload = {
          ...this.editForm,
          date_start: this.formatForDB(this.editForm.date_start),
          date_end: this.formatForDB(this.editForm.date_end)
        }

        await axios.put(
          `/api/event/${payload.id}`,
          payload,
          { withCredentials: true }
        )

        this.editDialog = false
        this.dialog = false

        await this.fetchEvents()
      } catch (err) {
        console.error('Neizdevās atjaunināt pasākumu:', err)
        alert('Neizdevās atjaunināt pasākumu')
      }
    },

    async deleteEvent() {
      if (!this.selectedEvent) return

      const id = this.selectedEvent.id

      try {
        await axios.delete(`/api/event/${id}`, {
          withCredentials: true
        })

        this.dialog = false
        this.selectedEvent = null

        await this.fetchEvents()
      } catch (err) {
        console.error('Neizdevās dzēst pasākumu:', err)
        alert('Neizdevās dzēst ierakstu')
      }
    },

    async deleteEventAdmin() {
      if (!this.selectedEvent) return

      const id = this.selectedEvent.id

      try {
        await axios.delete(`/api/event/admin/${id}`, {
          withCredentials: true
        })

        this.dialog = false
        this.selectedEvent = null

        await this.fetchEvents()
      } catch (err) {
        console.error('Neizdevās dzēst pasākumu:', err)
        alert('Neizdevās dzēst ierakstu')
      }
    }
  },

  async mounted() {
    this.fetchEvents()

    if (this.authStore.loading) {
      await this.authStore.fetchUser()
    }
  }
}
</script>

<style scoped>
.calendar-card { border-radius: 16px; }
.modern-calendar { min-height: 600px; }
@media (max-width: 960px) { .modern-calendar { min-height: 500px; } }
@media (max-width: 600px) { .modern-calendar { min-height: 420px; } }
</style>