<template>
  <v-container class="pa-4">
    <h1 class="text-center mt-8 mb-8">Pasākumu kalendārs</h1>
    <div class="mb-2 d-flex flex-row justify-end">
      <v-btn
        v-if="!loadingProfile && isLeader"
        color="secondary"
        rounded="xl"
        prepend-icon="mdi-plus"
        @click="$router.push('/calender/event')"
      >
        Pievienot pasākumu
      </v-btn>
    </div>
    <v-card class="mx-auto my-4" max-width="1200" outlined>
      <v-card-text>
          <v-row dense>
          <v-col v-for="(color, genre) in genreColorsLV" :key="genre" cols="auto">
            <v-chip :style="{ backgroundColor: color, color: '#fff' }" small pill>
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
    <v-dialog v-model="dialog" max-width="520">
      <v-card v-if="selectedEvent" rounded="xl" elevation="6" class="bg-secondary">
        <v-card-title class="text-h5 font-weight-bold d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-icon class="mr-3" color="white">mdi-calendar-star</v-icon>
            {{ selectedEvent.title }}
          </div>
          <div>
            <v-btn v-if="isEventCreator" icon="mdi-pencil" variant="text" @click="editEvent()"/>
            <v-btn v-if="isEventCreator || isAdmin" icon="mdi-delete" variant="text" color="red" @click="deleteEvent()"/>
          </div>
        </v-card-title>
        <v-divider />
        <v-card-text>
        <v-card-text>
          Izveidoja: {{ selectedEvent.extendedProps.dance_group_member.name }} ({{ genreLV(selectedEvent.extendedProps.dance_group_member.genre) }})
        </v-card-text>
          <v-list density="comfortable" class="bg-secondary">
            <v-list-item>
              <template #prepend>
                <v-icon color="white">mdi-map-marker</v-icon>
              </template>
              <v-list-item-title>
                {{ selectedEvent.extendedProps.location }}
              </v-list-item-title>
              <v-list-item-subtitle>Norises vieta</v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template #prepend>
                <v-icon color="white">mdi-clock-outline</v-icon>
              </template>
              <v-list-item-title>
                {{ formatDate(selectedEvent.start) }} - {{ formatDate(selectedEvent.end) }}
              </v-list-item-title>
              <v-list-item-subtitle>Laiks</v-list-item-subtitle>
            </v-list-item>
          </v-list>
          <v-divider class="my-4"/>
          <div class="text-body-1">{{ selectedEvent.extendedProps.description }}</div>
          <v-divider class="my-4"/>
          <div class="text-subtitle-1 font-weight-medium mb-3">Piedalās grupas</div>
          <div v-if="selectedEvent.extendedProps.dance_groups?.length">
            <v-chip
              v-for="group in selectedEvent.extendedProps.dance_groups"
              :key="group.id"
              class="ma-1"
              color="white"
              variant="tonal"
            >
              <strong class="mr-2">{{ group.dance_group.name }}:</strong>
              {{ group.name }} ({{ group.age_group }})
            </v-chip>
          </div>
          <v-alert v-else type="info" variant="tonal">
            Neviena grupa nepiedalās šajā pasākumā
          </v-alert>
        </v-card-text>
        <v-divider />
        <v-card-actions class="px-4 pb-4">
          <v-spacer/>
          <v-btn color="white" variant="tonal" @click="dialog=false">Aizvērt</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="editDialog" max-width="600">
      <v-card rounded="xl">
        <v-card-title class="text-h5 font-weight-bold">Rediģēt pasākumu</v-card-title>
        <v-divider/>
        <v-card-text>
          <v-text-field v-model="editForm.name" label="Pasākuma nosaukums"/>
          <v-text-field v-model="editForm.location" label="Vieta"/>
          <v-textarea v-model="editForm.description" label="Apraksts"/>
          <v-text-field v-model="editForm.date_start" label="Sākuma laiks" type="datetime-local"/>
          <v-text-field v-model="editForm.date_end" label="Beigu laiks" type="datetime-local"/>
        </v-card-text>
        <v-divider/>
        <v-card-actions>
          <v-spacer/>
          <v-btn variant="text" @click="editDialog=false">Atcelt</v-btn>
          <v-btn color="primary" @click="submitEditEvent">Saglabāt</v-btn>
        </v-card-actions>
      </v-card>
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

export default {
  components: { FullCalendar },
  data() {
    return {
      events: [],
      danceGroups: [],
      isLeader: false,
      loadingProfile: true,
      isEventCreator: false,
      dialog: false,
      isAdmin: true,
      editDialog: false,
      selectedEvent: null,
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
        'Liriskā deja': '#bf63b9',
        'Balets': '#1e90ff',
        'Mūsdienu deja': '#32cd32',
        'Tautas deja': '#ffa500',
        'Hip-hops': '#447574',
        'Cits': '#4f2552'
      },
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: lvLocale,
        initialView: 'dayGridMonth',
        headerToolbar: { right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        buttonText: { today: 'Šodien', month: 'Mēnesis', week: 'Nedēļa', day: 'Diena' },
        events: [],
        eventClick: (info) => this.openEventDialog(info.event)
      }
    }
  },
  computed: {
    monthTitle() {
      const calendarApi = this.$refs.fullCalendar?.getApi()
      return calendarApi ? calendarApi.view.title : ''
    }
  },
  methods: {
    async fetchEvents() {
      try {
        const res = await axios.get('/events')
        this.events = res.data.data || []

      const genreColors = { 
        'lyrical dance' : '#bf63b9', 
        'ballet' : '#1e90ff', 
        'contemporary dance' : '#32cd32', 
        'folk dance' : '#ffa500', 
        'hip hop': '#447574',
        'other' : '#4f2552' 
      }
        this.calendarOptions.events = this.events.map(e => {
          let genre = e.dance_group_member.genre
          let color = genreColors[genre] || '#808080'
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
      } catch (err) { console.error(err) }
    },
    async fetchProfileInfo() {
      try {
        const res = await axios.get('/api/leader-groups', { withCredentials: true })
        this.danceGroups = res.data || []
        this.isLeader = this.danceGroups.length > 0
      } catch {
        this.danceGroups = []
        this.isLeader = false
      } finally {
        this.loadingProfile = false
      }
    },
    genreLV(genre) {
      const map = {
        'lyrical dance': 'Liriskā dejas',
        'ballet': 'Balets',
        'contemporary dance': 'Mūsdienīgās dejas',
        'folk dance': 'Tautasdejas',
        'hip hop': 'Hip-hops',
        'other': 'Cits'
      }
      return map[genre] || genre
    },
    openEventDialog(event) {
      this.selectedEvent = event
      this.dialog = true
      this.eventeditMember()
    },
    formatDate(date) {
      return new Date(date).toLocaleString('lv-LV', { dateStyle: 'short', timeStyle: 'short' })
    },
    prev() { this.$refs.fullCalendar.getApi().prev() },
    next() { this.$refs.fullCalendar.getApi().next() },
    eventeditMember() {
      const eventMemberId = this.selectedEvent.extendedProps.dance_group_member?.id
      this.isEventCreator = this.danceGroups.some(group => group.member_id === eventMemberId)
    },
    editEvent() {
      const e = this.selectedEvent
      this.editForm = {
        id: e.id,
        name: e.title,
        location: e.extendedProps.location,
        description: e.extendedProps.description,
        date_start: this.formatForInput(e.start),
        date_end: this.formatForInput(e.end),
      }
      this.editDialog = true
    },
    formatForInput(date) {
      if (!date) return ''
      const d = new Date(date)
      const pad = n => n.toString().padStart(2, '0')
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`
    },
    formatForDB(date) {
      if (!date) return null
      const d = new Date(date)
      const pad = n => n.toString().padStart(2, '0')
      return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`
    },
    async submitEditEvent() {
      try {
        const payload = {
          ...this.editForm,
          date_start: this.formatForDB(this.editForm.date_start),
          date_end: this.formatForDB(this.editForm.date_end)
        }
        await axios.put(`/event/${payload.id}`, payload, { withCredentials: true })
        this.editDialog = false
        this.dialog = false
        this.fetchEvents()
      } catch (err) {
        console.error(err)
        alert('Neizdevās atjaunināt pasākumu')
      }
    },
    async deleteEvent() {
      const id = this.selectedEvent.id
      try {
        await axios.delete(`/event/${id}`, { withCredentials: true })
        this.dialog = false
        this.fetchEvents()
      } catch (err) {
        console.error(err)
        alert('Neizdevās dzēst ierakstu')
      }
    }
  },
  mounted() {
    this.fetchEvents()
    this.fetchProfileInfo()
  }
}
</script>

<style scoped>
.calendar-card { border-radius: 16px; }
.modern-calendar { min-height: 600px; }
@media (max-width: 960px) { .modern-calendar { min-height: 500px; } }
@media (max-width: 600px) { .modern-calendar { min-height: 420px; } }
</style>