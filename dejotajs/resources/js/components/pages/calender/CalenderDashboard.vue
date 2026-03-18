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
      <v-card v-if="selectedEvent" rounded="xl" elevation="6">
        <v-card-title class="text-h5 font-weight-bold d-flex align-center justify-space-between">
          <div class="d-flex align-center">
            <v-icon class="mr-3" color="primary">mdi-calendar-star</v-icon>
            {{ selectedEvent.title }}
          </div>
          <div v-if="isEventCreator">
            <v-btn icon="mdi-pencil" variant="text" @click="editEvent()"/>
            <v-btn icon="mdi-delete" variant="text" color="red" @click="deleteEvent(selectedEvent)"/>
          </div>
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-list density="comfortable">
            <v-list-item>
              <template #prepend>
                <v-icon color="primary">mdi-map-marker</v-icon>
              </template>
              <v-list-item-title>
                {{ selectedEvent.extendedProps.location }}
              </v-list-item-title>
              <v-list-item-subtitle>
                Norises vieta
              </v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template #prepend>
                <v-icon color="primary">mdi-clock-outline</v-icon>
              </template>
              <v-list-item-title>
                {{ formatDate(selectedEvent.start) }} - {{ formatDate(selectedEvent.end) }}
              </v-list-item-title>
              <v-list-item-subtitle>
                Laiks
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
          <v-divider class="my-4" />
          <div class="text-body-1">
            {{ selectedEvent.extendedProps.description }}
          </div>
        </v-card-text>
        <v-divider />
        <v-card-text>
          <div class="text-subtitle-1 font-weight-medium mb-3">
            Piedalās grupas
          </div>
          <div v-if="selectedEvent.extendedProps.dance_groups?.length">
            <v-chip
              v-for="group in selectedEvent.extendedProps.dance_groups"
              :key="group.id"
              class="ma-1"
              color="primary"
              variant="tonal"
            >
              <strong class="mr-2">
                {{ group.dance_group.name }}:
              </strong>
              {{ group.name }} ({{ group.age_group }})
            </v-chip>
          </div>
          <v-alert v-else type="info" variant="tonal">
            Neviena grupa nepiedalās šajā pasākumā
          </v-alert>
        </v-card-text>
        <v-card-actions class="px-4 pb-4">
          <v-spacer />
          <v-btn
            color="primary"
            variant="tonal"
            @click="dialog = false"
          >
            Aizvērt
          </v-btn>
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
  components: {
    FullCalendar
  },
  data() {
    return {
      events: [],
      danceGroups: [],
      isLeader: false,
      loadingProfile: true,
      isEventCreator: false,
      dialog: false,
      selectedEvent: null,
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin
        ],
        locale: lvLocale,
        initialView: 'dayGridMonth',
        selectable: true,
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
        eventClick: (info) => {
          this.openEventDialog(info.event)
        }
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
        this.events = Array.isArray(res.data.data) ? res.data.data : []
        this.calendarOptions.events = this.events.map(e => ({
          id: e.id,
          title: e.name,
          start: e.date_start,
          end: e.date_end || e.date_start,
          extendedProps: {
            location: e.location,
            description: e.description,
            dance_groups: e.dance_groups,
            dance_group_member: e.dance_group_member
          }
        }))
      } catch (err) {
        console.error(err)
      }
    },
    async fetchProfileInfo() {
      try {
        const res = await axios.get(
          '/api/leader-groups',
          { withCredentials: true }
        )
        this.danceGroups = Array.isArray(res.data) ? res.data : []
        this.isLeader = this.danceGroups.length > 0
      } catch {
        this.danceGroups = []
        this.isLeader = false
      } finally {
        this.loadingProfile = false
      }
    },
    openEventDialog(event) {
      this.selectedEvent = event
      this.dialog = true

      this.eventeditMember() 
    },
    formatDate(date) {
      return new Date(date).toLocaleString(
        'lv-LV',
        { dateStyle: 'short', timeStyle: 'short' }
      )
    },
    prev() {
      this.$refs.fullCalendar.getApi().prev()
    },
    next() {
      this.$refs.fullCalendar.getApi().next()
    },
    editEvent() {
      console.log("Edit event:", this.selectedEvent.id)
    },
    async deleteEvent() {
      const id = this.selectedEvent.id
      try {
        await axios.delete(`/event/${id}`, { withCredentials: true })
        this.dialog =false,
        this.fetchEvents()
      } catch (err) {
        console.error('Kļūda dzēšot ierakstu:', err)
        alert('Neizdevās dzēst ierakstu')
      }
    },
    eventeditMember() {
      const eventMemberId = this.selectedEvent.extendedProps.dance_group_member.id
      this.isEventCreator = this.danceGroups.some(group => 
        group.member_id === eventMemberId
      )
    }
  },
  mounted() {
    this.fetchEvents()
    this.fetchProfileInfo()
  }
}
</script>
<style scoped>
.calendar-card {
  border-radius: 16px;
}
.modern-calendar {
  min-height: 600px;
}
@media (max-width: 960px) {
  .modern-calendar {
    min-height: 500px;
  }
}
@media (max-width: 600px) {
  .modern-calendar {
    min-height: 420px;
  }
}
</style>