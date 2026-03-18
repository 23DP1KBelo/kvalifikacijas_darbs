<template>
  <v-container class="d-flex justify-center align-center mt-8">
    <v-card class="bg-softblue" elevation="16" width="500px">
      <v-card-title class="text-center mb-4 mt-4">Pasākumu izveidošana</v-card-title>
      <v-card-text>
        <v-form ref="form" v-model="valid">
          <v-col cols="12">
            <div v-if="!selectedGroup">
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
            </div>
          </v-col>
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
                <v-text-field
                  v-model="startDateTime"
                  label="Sākuma datums un laiks"
                  type="datetime-local"
                  variant="outlined"
                  density="comfortable"
                />
                <v-text-field
                  v-model="endDateTime"
                  label="Beigu datums un laiks"
                  type="datetime-local"
                  variant="outlined"
                  density="comfortable"
                />
              </v-col>
              <v-alert v-if="error" type="error" dense outlined class="mt-3">
                {{ error }}
              </v-alert>
              <v-btn color="primary" class="mt-3" @click="submitEventForm">Saglabāt</v-btn>
            </div>
          </v-slide-y-transition>
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
              <v-alert type="info" >
                Lūdzu izvēleities visas deju grupas, vēlāk mainīt grupas nebūs iespējams!
              </v-alert>
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