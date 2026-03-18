<template>
  <v-container class="pa-4">
    <h1 class="text-center mt-8 mb-8">Pasākumu kalendārs</h1>
    <div class="mb-2 d-flex flex-row justify-end" v-if="danceGroups.length > 0" >
      <v-btn color="secondary" rounded="xl" prepend-icon="mdi-plus" @click="$router.push('/calender/event')">
        Pievienot pasākumu
      </v-btn>
    </div>
    <v-card class="mx-auto calendar-card" max-width="1200">
      <v-card-title class="d-flex align-center justify-space-between">
        <v-btn icon="mdi-chevron-left" variant="text" @click="prev" />
        <div class="text-h6 font-weight-medium">
          {{ monthTitle }}
        </div>
        <v-btn icon="mdi-chevron-right" variant="text" @click="next" />
      </v-card-title>
      <v-divider />
      <v-card-text>
      <v-calendar
        ref="calendar"
        v-model="focus"
        view-mode="month"
        locale="lv"
        color="text"
        class="modern-calendar bg-surface"
        :events="calendarEvents"
      >
        <template #event="{ event }">
          <div class="calendar-event mx-2" @click="showEvent(event)">
            {{ event.title }}
          </div>
        </template>
      </v-calendar>
      </v-card-text>
    </v-card>
    <v-dialog v-model="dialog" max-width="520">
      <v-card v-if="selectedEvent" rounded="xl" elevation="6">
        <v-card-title class="text-h5 font-weight-bold d-flex align-center">
          <v-icon class="mr-3" color="primary">mdi-calendar-star</v-icon>
          {{ selectedEvent.title }}
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-list density="comfortable">
            <v-list-item>
              <template #prepend>
                <v-icon color="primary">mdi-map-marker</v-icon>
              </template>
              <v-list-item-title>
                {{ selectedEvent.location }}
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
                {{ selectedEvent.start }} – {{ selectedEvent.end }}
              </v-list-item-title>
              <v-list-item-subtitle>
                Laiks
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
          <v-divider class="my-4" />
          <div class="text-body-1">
            {{ selectedEvent.description }}
          </div>
        </v-card-text>
        <v-divider />
        <v-card-text>
          <div class="text-subtitle-1 font-weight-medium mb-3">
            Piedalās grupas
          </div>
          <div v-if="selectedEvent.dance_groups?.length > 0">
            <v-chip v-for="group in selectedEvent.dance_groups" :key="group.id" class="ma-1" color="primary" variant="tonal">
              <strong class="mr-2">{{ group.dance_group.name }}:</strong>{{ group.name }} ({{ group.age_group }})
            </v-chip>
          </div>
          <v-alert v-else type="info" variant="tonal">
            Neviena grupa nepiedalās šajā pasākumā
          </v-alert>
        </v-card-text>
        <v-card-actions class="px-4 pb-4">
          <v-spacer/>
          <v-btn color="primary" variant="tonal" @click="dialog = false">
            Aizvērt
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios'
export default {
  data() {
    return {
      focus: new Date(),
      events: [],
      dialog: false,
      selectedEvent: null,
      danceGroups: []
    }
  },
  computed: {
    monthTitle() {
      return new Intl.DateTimeFormat("lv-LV", {
        month: "long",
        year: "numeric",
      }).format(this.focus)
    },
    calendarEvents() {
        return this.events.map(event => ({
          title: event.name,
          start: event.date_start,
          end: event.date_end || event.date_start,
          location: event.location,
          description: event.description,
          dance_groups: event.dance_groups,
          color: "secondary"
        }))
      }
  },
  methods: {
    prev() {
      this.$refs.calendar.prev()
    },

    next() {
      this.$refs.calendar.next()
    },

    showEvent(event) {
      console.log(event)
      this.selectedEvent = event
      this.dialog = true
    },
    fetchEvents() {
        axios
        .get('/events')
            .then(res => {
              this.events = Array.isArray(res.data.data) ? res.data.data : []
            })
            .catch(err => console.log(err))
    },
    async fetchProfileInfo() { 
      try { 
        const res = await axios.get('/api/leader-groups', { withCredentials: true }); 
        console.log("Leader groups response:", res.data); 
        this.danceGroups = Array.isArray(res.data) ? res.data : []; 
      } catch(err) { 
          console.error('Neizdevās ielādēt leader grupas:', err);
          this.danceGroups = [];
      }
    }
  },
  mounted() {
    if (this.isLoggedIn) {
      this.fetchEvents();
      this.fetchProfileInfo();
    } else {
      this.fetchEvents();
      this.danceGroups = [];
    }
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