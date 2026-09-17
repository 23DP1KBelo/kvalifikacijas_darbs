<template>
  <v-container fluid class="pa-0">
    <v-row justify="center" class="fill-height">
      <v-col
        cols="12"
        md="10"
        lg="9"
      >
        <v-card
          elevation="2"
          class="rounded-0 mt-8"
          min-height="600"
        >
          <v-row no-gutters class="h-100">
            <!-- KREISĀ PUSE -->
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column pa-8"
            >
              <!-- Atpakaļ poga -->
              <div class="d-flex justify-start">
                <v-btn
                  variant="text"
                  @click="this.$router.push('/')"
                >
                <v-icon>mdi-arrow-left</v-icon>
                </v-btn>
              </div>
              <h2
                class="text-accent text-accents mt-6 text-uppercase text-center"
              >
                MANI KOLEKTĪVI
              </h2>

              <v-divider class="my-7"></v-divider>
              <div v-if="approvedGroups.length === 0" class="text-center text-white">
                Šobrīd nav apstiprinātu kolektīvu.
              </div>
              <div v-else>
                <v-alert
                  v-if="error"
                  v-model="showError"
                  type="warning"
                  closable
                  class="mb-4"
                  @update:model-value="error = ''"
                >
                  {{ error }}
              </v-alert>
              <v-row>
                <v-col
                  v-for="group in approvedGroups"
                  :key="group.id"
                  cols="12"
                  sm="6"
                  class="d-flex justify-center"
                >
                  <v-card 
                    class="pa-4 w-100" 
                    elevation="8"
                    @click="openGroup(group)"
                  >
                    <v-card-title class="text-h6 text-center text-wrap text-break">{{ group.name }}</v-card-title>
                    <v-card-subtitle class="text-center text-wrap text-break">
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
              </div>
            </v-col>
            <!-- LABĀ PUSE -->
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column pa-8 order-first order-md-last mt-5"
            >
              <h2
                class="text-accent text-accents mt-10 text-uppercase text-center"
              >
                PROFILS
              </h2>
              <div class="d-flex align-center justify-space-between mt-4 text-small">
                <h2 class="text-small">
                  Par mani
                </h2>
                <v-btn
                  variant="text"
                >
                  <v-icon>mdi-pencil-outline</v-icon>
                </v-btn>
              </div>

              <v-divider class="my-2"></v-divider>
              <div class="mb-10">
                  <h3 class="text-small">
                    {{ user.name }} {{ user.surname }}
                  </h3>
                  <h4 class="text-small mt-2">
                    {{ user.email }}
                  </h4>
                  <h4 class="text-small mt-5">
                    +371 {{ user.phone_number }}
                  </h4>
              </div>
              <v-divider class="my-2"></v-divider>
              <div class="mt-6 text-right">
                <v-btn
                  class="bg-accent text-small"
                  @click="showLeaderDialog = true"
                >
                  Kļūt par vadītāju
                </v-btn>
              </div>
              <!-- Kļūt par vadītāju. Forma kolektīva izveidei un pievienošanās esošam kolektīvam. -->
              <v-dialog
                v-model="showLeaderDialog"
                max-width="500"
              >
                <v-card class="pa-4">
                  <v-card-title class="text-h6 text-center">
                    Kļūt par vadītāju
                  </v-card-title>

                  <v-card-text class="text-center text-wrap text-para">
                    Vai vēlies kļūt par vadītāju jau esošā kolektīvā?
                  </v-card-text>

                  <v-card-actions class="justify-center">
                    <v-btn
                      variant="outlined"
                      @click="goTo('/danceGroup-list')"
                    >
                      Jā
                    </v-btn>

                    <v-btn
                      class="bg-accent"
                      @click="goTo('/create-dance-group')"
                    >
                      Nē
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
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
      showError: false,
      showLeaderDialog: false,
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
    goTo(route) {
      this.drawer = false
      this.$router.push(route)
    },
    openGroup(group){
      if(group.status != 'approved') {
        this.error = 'Jūsu dalība šajā kolektīvā vēl nav apstiprināta.'
        this.showError = true
        return;
      }
      this.goTo(`group/${group.id}`)
    }
  },

  async mounted() {
    try {
      await this.authStore.fetchProfile()
      console.log()
    } catch (err) {
      this.error = 'Neizdevās ielādēt profilu'
    }
  },
}
</script>


