
<template>
  <v-container fluid class="ma-0 pa-0">
    <h2 class="text-accent text-accents text-center mt-6">
      PASĀKUMS
    </h2>

    <!-- 1. solis: pasākuma informācija -->
    <v-row
      v-if="!save"
      justify="center"
      class="mt-8"
    >
      <v-col cols="12" sm="10" md="8" lg="6" xl="5">
        <v-form ref="form" v-model="valid">
          <v-select
            v-if="!selectedGroup"
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

          <v-text-field
            v-model="name"
            label="Pasākuma nosaukums"
            variant="outlined"
            :disabled="!selectedGroup"
            :rules="[v => !!v || 'Pasākuma nosaukums ir obligāts']"
            required
          />

          <v-text-field
            v-model="location"
            label="Atrašanās vieta"
            variant="outlined"
            :disabled="!selectedGroup"
            :rules="[v => !!v || 'Atrašanās vieta ir obligāta']"
            required
          />

          <v-textarea
            v-model="description"
            label="Pasākuma apraksts"
            variant="outlined"
            :disabled="!selectedGroup"
            rows="3"
          />

          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                v-model="startDateTime"
                label="Sākuma datums un laiks"
                type="datetime-local"
                variant="outlined"
                :disabled="!selectedGroup"
                :rules="[v => !!v || 'Norādi sākuma laiku']"
                required
              />
            </v-col>

            <v-col cols="12" sm="6">
              <v-text-field
                v-model="endDateTime"
                label="Beigu datums un laiks"
                type="datetime-local"
                variant="outlined"
                :disabled="!selectedGroup"
                :rules="[v => !!v || 'Norādi beigu laiku']"
                required
              />
            </v-col>
          </v-row>

          <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            class="mb-4"
          >
            {{ error }}
          </v-alert>

          <div class="d-flex justify-end mt-6">
            <v-btn
              class="bg-accent text-small text-none"
              :disabled="!selectedGroup"
              @click="submitEventForm"
            >
              Turpināt
              <v-icon end>mdi-arrow-right</v-icon>
            </v-btn>
          </div>
        </v-form>
      </v-col>
    </v-row>

    <!-- 2. solis: deju grupu izvēle -->
    <v-row
      v-if="save"
      justify="center"
      class="mt-8"
    >
      <v-col cols="12" sm="10" md="9" lg="8" xl="7">
        <v-form>
          <v-select
            v-model="selectedAgeGroups"
            :items="ageGroups"
            :item-title="ageGroup => `${ageGroup.dance_group.name} (${ageGroup.name} ${ageGroup.age_group})`"
            item-value="id" 
            label="Izvēlies deju grupas"
            variant="outlined"
            density="comfortable"
            prepend-icon="mdi-dance-ballroom"
            multiple
          />

          <v-alert type="info" variant="tonal" class="mt-3">
            Lūdzu, izvēlies visas deju grupas. Vēlāk mainīt grupas nebūs iespējams!
          </v-alert>

          <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            class="mt-4"
          >
            {{ error }}
          </v-alert>

          <div class="d-flex justify-center ga-4 mt-8">
            <v-btn
              variant="outlined"
              @click="save = false; error = ''"
            >
              <v-icon start>mdi-arrow-left</v-icon>
              Atpakaļ
            </v-btn>

            <v-btn
              class="bg-accent text-small text-none"
              @click="attachAgeGroups"
            >
              Saglabāt grupas
              <v-icon end>mdi-check</v-icon>
            </v-btn>
          </div>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      valid: false,
      name: '',
      location: '',
      description: '',
      startDateTime: '',
      endDateTime: '',
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
    validateForm() {
      if (!this.name || !this.startDateTime || !this.endDateTime || !this.location || !this.selectedGroup) {
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

    async fetchGroups() {
      try {
        const res = await axios.get('/api/ageGroups', { withCredentials: true })
        this.ageGroups = res.data.data
      } catch (err) {
        console.error('Kļūda ielādējot deju grupas:', err.response?.data || err.message)
      }
    },

    async fetchProfileInfo() {
      try {
        const res = await axios.get('/api/leader-groups', { withCredentials: true })
        this.groups = Array.isArray(res.data) ? res.data : []
      } catch(err) {
        console.error(err)
        this.groups = []
      }
    }
  },
  mounted() {
    this.fetchGroups()
    this.fetchProfileInfo()
  }
}
</script>