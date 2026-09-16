<template >
  <v-container class="py-8">
    <h1 class="text-center mb-8">Lietotāja profils</h1>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card color="primary" elevation="16" class="mx-auto mb-6 pa-6">
          <v-card-title class="text-center text-h4 text-white">
            {{ user.name }} {{ user.surname }}
          </v-card-title>
          <v-card-text class="text-center text-white mb-4">
            E-pasts: {{ user.email }}
          </v-card-text>

          <v-divider class="my-4" />

          <div v-if="approvedGroups.length === 0" class="text-center text-white">
            Šobrīd nav apstiprinātu kolektīvu.
          </div>

          <v-row v-else>
            <v-col
              v-for="group in approvedGroups"
              :key="group.id"
              cols="12"
              sm="6"
              class="d-flex justify-center"
            >
              <v-card class="pa-4 w-100" elevation="8">
                <v-card-title class="text-h6 text-center">{{ group.name }}</v-card-title>
                <v-card-subtitle class="text-center">
                  <template v-if="group.role === 'leader'">
                    Vadītājs ({{ translateStatus(group.status) }})
                  </template>
                  <template v-else>
                    Dejotājs, Vecuma grupa: {{ group.age_group || 'Nav norādīta' }}
                  </template>
                </v-card-subtitle>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'Profile',

  data() {
    return {
      authStore: useAuthStore(),

      statusMap: {
        approved: 'Apstiprināts',
        waiting: 'Gaida apstiprinājumu',
      },

      error: '',
    }
  },

  computed: {
    user() {
      return this.authStore.user
    },

    danceGroups() {
      return this.user?.dance_groups || []
    },

    approvedGroups() {
      return this.danceGroups.filter(
        group =>
          group.status === 'approved' ||
          group.status === 'waiting'
      )
    },
  },

  methods: {
    translateStatus(status) {
      return this.statusMap[status] || status
    },
  },

  async mounted() {
    try {
      await this.authStore.fetchProfile()
    } catch (err) {
      this.error = 'Neizdevās ielādēt profilu'
    }
  },
}
</script>


