<template>
  <v-container class="d-flex justify-center align-center mt-8">
    <v-card class="bg-softblue" elevation="16" width="500px">
      <v-card-title class="text-center mb-4 mt-4">Pasākumu izveidošana</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid">
          <!-- 1️⃣ Event dati -->
          <div v-if="!save">
            <v-row dense>
              <v-col cols="12">
                <v-text-field
                  v-model="name"
                  label="Pasākuma nosaukums"
                  variant="outlined"
                  density="comfortable"
                ></v-text-field>
                <v-text-field
                v-model="location"
                label="Atrašanās vieta"
                variant="outlined"
                density="comfortable"
                >
                </v-text-field>
                <v-textarea
                    v-model="description"
                    label="Pasākuma apraksts"
                    variant="outlined"
                    density="comfortable"
                >
                </v-textarea>
              </v-col>

              <!-- Sākuma datums + laiks -->
              <v-col cols="12" md="6">
                <v-menu v-model="menuStart" :close-on-content-click="false" offset-y>
                  <template #activator="{ props }">
                    <v-text-field
                      v-model="startDateTime"
                      label="Sākuma datums un laiks"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="props"
                    ></v-text-field>
                  </template>
                  <v-card>
                    <v-date-picker v-model="startDate" @input="updateStartDateTime"></v-date-picker>
                    <v-divider></v-divider>
                    <v-time-picker v-model="startTime" format="24hr" @input="updateStartDateTime"></v-time-picker>
                  </v-card>
                </v-menu>
              </v-col>

              <!-- Beigu datums + laiks -->
              <v-col cols="12" md="6">
                <v-menu v-model="menuEnd" :close-on-content-click="false" offset-y>
                  <template #activator="{ props }">
                    <v-text-field
                      v-model="endDateTime"
                      label="Beigu datums un laiks"
                      prepend-icon="mdi-calendar"
                      readonly
                      v-bind="props"
                    ></v-text-field>
                  </template>
                  <v-card>
                    <v-date-picker v-model="endDate" @input="updateEndDateTime"></v-date-picker>
                    <v-divider></v-divider>
                    <v-time-picker v-model="endTime" format="24hr" @input="updateEndDateTime"></v-time-picker>
                  </v-card>
                </v-menu>
              </v-col>
            </v-row>

            <v-alert v-if="error" type="error" dense outlined class="mt-3">
              {{ error }}
            </v-alert>

            <v-btn color="primary" @click="submitEventForm">Saglabāt</v-btn>
          </div>

          <!-- 2️⃣ Age groups multi-select -->
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
              ></v-select>
            </v-col>

            <v-alert v-if="error" type="error" dense outlined class="mt-3">
              {{ error }}
            </v-alert>

            <v-btn color="primary" @click="attachAgeGroups">Saglabāt grupas</v-btn>
          </div>
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
      location: null,
      description: null,
      startDateTime: '',
      endDateTime: '',
      menuStart: false,
      menuEnd: false,
      save: false,
      selectedAgeGroups: [],
      ageGroups: [],
      createdEventId: null,
      error: ''
    }
  },
  methods: {
    // Apvieno datumu un laiku datetime string formātā
    formatDateTime(date, time) {
        if (!date || !time) return null

        // Ja date ir JS Date objekts, pārveidojam uz YYYY-MM-DD
        let d = date instanceof Date ? date : new Date(date)
        const year = d.getFullYear()
        const month = String(d.getMonth() + 1).padStart(2, '0')
        const day = String(d.getDate()).padStart(2, '0')

        // time jāsaglabā kā "HH:MM:SS"
        let [hours, minutes] = time.split(':')
        hours = String(hours).padStart(2, '0')
        minutes = String(minutes).padStart(2, '0')

        return `${year}-${month}-${day} ${hours}:${minutes}:00`
    },

    updateStartDateTime() {
      this.startDateTime = this.formatDateTime(this.startDate, this.startTime)
    },

    updateEndDateTime() {
      this.endDateTime = this.formatDateTime(this.endDate, this.endTime)
    },

    async submitEventForm() {
    this.error = ''

    if (!this.name || !this.startDate || !this.startTime || !this.endDate || !this.endTime || !this.location) {
        this.error = 'Lūdzu aizpildiet visus laukus.'
        return
    }

    try {
        const payload = {
            name: this.name,
            description: this.description,
            date_start: this.formatDateTime(this.startDate, this.startTime),
            date_end: this.formatDateTime(this.endDate, this.endTime),
            location: this.location,
            dance_group_member_id: 1
        }

        const res = await axios.post('/api/events/create', payload, { withCredentials: true })

        if (res.data && res.data.data && res.data.data.id) {
            this.createdEventId = res.data.data.id
            this.save = true
        } else {
            console.error('Event ID nav atgriezts:', res.data)
            this.error = 'Neizdevās saņemt event ID no servera'
        }
    } catch (err) {
        console.error(err.response?.data || err.message)
        this.error = err.response?.data?.message || 'Nezināma kļūda'
    }
},

    // 2️⃣ Attach age groups
    async attachAgeGroups() {
      this.error = ''
      if (!this.selectedAgeGroups.length) {
        this.error = 'Lūdzu izvēlieties vismaz vienu deju grupu.'
        return
      }

      try {
        console.log(this.createdEventId)
        await axios.post(`/api/${this.createdEventId}/attach`, {
            age_group_ids: this.selectedAgeGroups
        }, { withCredentials: true })

        alert('Pasākums un grupas veiksmīgi saglabātas!')
        this.$router.push('/') // pārvieto uz sarakstu vai dashboard
      } catch (err) {
        console.error(err.response?.data || err.message)
        this.error = err.response?.data?.message || 'Neizdevās pievienot grupas'
      }
    },

    // Iegūst visas deju grupas
    async fetchGroup() {
      try {
        const res = await axios.get('/api/ageGroups', { withCredentials: true })
        this.ageGroups = res.data.data
      } catch (err) {
        console.error('Kļūda ielādējot deju grupas:', err.response?.data || err.message)
      }
    }
  },
  mounted() {
    this.fetchGroup()
  }
}
</script>