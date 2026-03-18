<template>
  <v-container class="d-flex justify-center align-center mt-8">
    <v-card class="bg-softblue" elevation="16" width="500px">
      <v-card-title class="text-center mb-4 mt-4">Pasākumu izveidošana</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid">
          
          <!-- 1️⃣ Kolektīva izvēle -->
          <v-col cols="12">
            <v-select
              v-model="selectedGroup"
              :items="groups"
              item-title="name"
              item-value="member_id"
              label="Kuram kolektīvam veidots pasākums"
              variant="outlined"
              density="comfortable"
              clearable
              prepend-icon="mdi-dance-ballroom"
            />
          </v-col>

          <!-- 2️⃣ Pasākuma informācija -->
          <v-slide-y-transition>
          <div v-if="selectedGroup && !save">
            <v-col cols="12">
              <v-text-field
                v-model="name"
                label="Pasākuma nosaukums"
                variant="outlined"
                density="comfortable"
              />
              <v-text-field
                v-model="location"
                label="Atrašanās vieta"
                variant="outlined"
                density="comfortable"
              />
              <v-textarea
                v-model="description"
                label="Pasākuma apraksts"
                variant="outlined"
                density="comfortable"
              />
            </v-col>

            <!-- Datums un laiks -->
            <v-row dense>
              <v-col cols="12" md="6">
                <v-menu
                  v-model="menuStart"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  max-width="290"
                  min-width="290"
                >
                  <template #activator="{ props }">
                    <v-text-field
                      v-model="startDateTime"
                      label="Sākuma datums un laiks"
                      prepend-icon="mdi-calendar-clock"
                      readonly
                      v-bind="props"
                    />
                  </template>
                  <v-card>
                    <v-date-picker
                      v-model="startDate"
                      @update:model-value="updateStartDateTime"
                    />
                    <v-time-picker
                      v-model="startTime"
                      format="24hr"
                      @update:model-value="updateStartDateTime"
                      show-seconds
                    />
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="menuStart = false">OK</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </v-col>

              <v-col cols="12" md="6">
                <v-menu
                  v-model="menuEnd"
                  :close-on-content-click="false"
                  transition="scale-transition"
                  max-width="290"
                  min-width="290"
                >
                  <template #activator="{ props }">
                    <v-text-field
                      v-model="endDateTime"
                      label="Beigu datums un laiks"
                      prepend-icon="mdi-calendar-clock"
                      readonly
                      v-bind="props"
                    />
                  </template>
                  <v-card>
                    <v-date-picker
                      v-model="endDate"
                      @update:model-value="updateEndDateTime"
                    />
                    <v-time-picker
                      v-model="endTime"
                      format="24hr"
                      @update:model-value="updateEndDateTime"
                      show-seconds
                    />
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn text color="primary" @click="menuEnd = false">OK</v-btn>
                    </v-card-actions>
                  </v-card>
                </v-menu>
              </v-col>
            </v-row>

            <v-alert v-if="error" type="error" dense outlined class="mt-3">
              {{ error }}
            </v-alert>

            <v-btn color="primary" class="mt-3" @click="submitEventForm">Saglabāt</v-btn>
          </div>
          </v-slide-y-transition>

          <!-- 3️⃣ Age groups izvēle -->
          <v-slide-y-transition>
          <div v-if="save">
            <v-col cols="12">
              <v-select
                v-model="selectedAgeGroups"
                :items="ageGroups"
                :item-title="ageGroup => `${ageGroup.name} (${ageGroup.age_group})`"
                item-value="id"
                label="Izvēlies deju grupas"
                variant="outlined"
                density="comfortable"
                clearable
                prepend-icon="mdi-dance-ballroom"
                multiple
              />
            </v-col>

            <v-alert v-if="error" type="error" dense outlined class="mt-3">
              {{ error }}
            </v-alert>

            <v-btn color="primary" class="mt-3" @click="attachAgeGroups">Saglabāt grupas</v-btn>
          </div>
          </v-slide-y-transition>

        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      valid: false,
      name: '',
      startDate: null,
      startTime: null,
      endDate: null,
      endTime: null,
      location: '',
      description: '',
      startDateTime: '',
      endDateTime: '',
      menuStart: false,
      menuEnd: false,
      save: false,
      selectedAgeGroups: [],
      ageGroups: [],
      groups: [],
      selectedGroup: null,
      createdEventId: null,
      error: ''
    }
  },
  methods: {
    formatDateTime(date, time) {
      if (!date || !time) return ''
      const d = new Date(date)
      const year = d.getFullYear()
      const month = String(d.getMonth() + 1).padStart(2, '0')
      const day = String(d.getDate()).padStart(2, '0')
      let [hours, minutes] = time.split(':')
      return `${year}-${month}-${day} ${hours}:${minutes}:00`
    },

    updateStartDateTime() {
      this.startDateTime = this.formatDateTime(this.startDate, this.startTime)
    },

    updateEndDateTime() {
      this.endDateTime = this.formatDateTime(this.endDate, this.endTime)
    },

    validateForm() {
      if (!this.name || !this.startDate || !this.startTime || !this.endDate || !this.endTime || !this.location) {
        this.error = 'Lūdzu aizpildiet visus laukus.'
        return false
      }
      this.error = ''
      return true
    },

    async submitEventForm() {
      if (!this.validateForm()) return
      try {
        const payload = {
          name: this.name,
          description: this.description,
          date_start: this.startDateTime,
          date_end: this.endDateTime,
          location: this.location,
          dance_group_member_id: this.selectedGroup
        }
        const res = await axios.post('/api/events/create', payload, { withCredentials: true })
        if (res.data?.data?.id) {
          this.createdEventId = res.data.data.id
          this.save = true
        } else {
          this.error = 'Neizdevās saņemt event ID no servera'
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Nezināma kļūda'
      }
    },

    async attachAgeGroups() {
      if (!this.selectedAgeGroups.length) {
        this.error = 'Lūdzu izvēlieties vismaz vienu deju grupu.'
        return
      }
      try {
        await axios.post(`/api/${this.createdEventId}/attach`, { age_group_ids: this.selectedAgeGroups }, { withCredentials: true })
        alert('Pasākums un grupas veiksmīgi saglabātas!')
        this.$router.push('/calender')
      } catch (err) {
        this.error = err.response?.data?.message || 'Neizdevās pievienot grupas'
      }
    },
    async fetchGroup() { 
      try { 
        const res = await axios.get('/api/ageGroups', { withCredentials: true }) 
        this.ageGroups = res.data.data 
      } catch (err) { 
        console.error('Kļūda ielādējot deju grupas:', err.response?.data || err.message) 
      }
    }, 
    async fetchProfileInfo() { 
      try { 
        const res = await axios.get('/api/leader-groups', { withCredentials: true }); 
        console.log("Leader groups response:", res.data); 
        this.groups = Array.isArray(res.data) ? res.data : []; 
      } catch(err) { 
        console.error(err); 
        this.groups = []; 
      }
    }
  },
  mounted() {
    this.fetchGroups()
    this.fetchAgeGroups()
  }
}
</script>