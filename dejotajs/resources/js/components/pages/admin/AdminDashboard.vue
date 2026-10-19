<template>
  <v-container fluid class="pa-4 pa-md-8">
    <!-- Lapas galvene -->
    <div class=" mb-8">
      <div>
        <h1 class="">
          ADMINISTRĒŠANA
        </h1>

        <p class="text-body-2 text-small mt-2">
        Pārvaldi pieteikumus, lietotājus, kolektīvus, pasākumus un ierakstus.
      </p>
      </div>
    </div>
    <!-- Kopsavilkuma kartītes -->
    <v-row dense class="mb-8 ">
      <v-col cols="12" sm="3">
        <!-- Lietotaji -->
        <v-card 
          class="pa-4" 
          elevation="1"
        >
          <div class="d-flex align-center  ga-5 mt-5">
              <v-icon color="blue-darken-2">
                mdi-account-group-outline
              </v-icon>

            <div class="ml-6">
              <div class="text-h5 font-weight-bold">
                  Lietotāji {{ users.length }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="3">
        <!-- Kolektivi -->
         <v-card 
            class=" pa-4" 
            elevation="1"
          >
          <div class="d-flex align-center ga-5 mt-5">
              <v-icon color="green-darken-2">
                mdi-dance-ballroom
              </v-icon>
              <div class="ml-6">
              <div class="text-h5 font-weight-bold">
                   Kolektīvi {{ allGroups.length }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
      <v-col cols="12" sm="2">
        <v-card 
          class="pa-4" 
          elevation="1"
        >
          <div class="d-flex align-center ga-2 mt-5">
              <v-icon color="yellow-darken-2">
                mdi-text-box-multiple
              </v-icon>
              <div class="ml-6">
              <div class="text-h6 font-weight-bold">
                  Ieraksti {{ allGroups.length }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="2">
         <v-card 
            class="pa-4" 
            elevation="1"
          >
          <div class="d-flex align-center ga-2 mt-5">
              <v-icon color="blue-darken-2">
                mdi-calendar-month
              </v-icon>
              <div class="ml-6">
              <div class="text-h6 font-weight-bold">
                  Pasākumi {{ allGroups.length }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Kļūdas paziņojums -->
    <v-alert
      v-if="pageError"
      type="error"
      variant="tonal"
      closable
      class="mb-6"
      @click:close="pageError = ''"
    >
      {{ pageError }}
    </v-alert>
    <!-- Apstiprinājumi -->
    <section class="mb-10">
      <v-expansion-panels variant="accordion">
        <v-expansion-panel elevation="0">
          <v-expansion-panel-title class="px-0">
            <div class="d-flex align-center ga-5">
              <h2 class="text-small">
                Apstiprinājumi
              </h2>

              <v-chip color="red-darken-2" variant="tonal">
                {{ groups.length }}
              </v-chip>
            </div>
          </v-expansion-panel-title>

          <v-expansion-panel-text>
            <v-progress-linear
              v-if="loadingGroups"
              indeterminate
              color="primary"
              class="mb-4"
            />
            <v-row v-if="groups.length" dense>
              <v-col
                v-for="group in groups"
                :key="group.id"
                cols="12"
                sm="6"
                lg="4"
              >
                <v-card class="h-100 d-flex flex-column mt-6" elevation="3">
                  <v-card-text class="pa-5 flex-grow-1">
                    <h3 class="text-h6 font-weight-bold mb-2">
                      {{ group.name }}
                    </h3>

                    <p class="text-body-2 text-medium-emphasis mb-4">
                      {{ group.city || 'Pilsēta nav norādīta' }},
                      <span v-if="group.address">
                        {{ group.address }}
                      </span>
                    </p>

                    <v-btn
                      variant="text"
                      color="accent"
                      size="small"
                      prepend-icon="mdi-information-outline"
                      @click="openGroupInfo(group)"
                    >
                      Skatīt informāciju
                    </v-btn>
                  </v-card-text>

                  <v-divider />
                  <v-card-actions class="pa-4 flex-wrap">
                    <v-btn
                      color="success"
                      variant="tonal"
                      prepend-icon="mdi-check"
                      :loading="processingId === group.id"
                      :disabled="!!processingId"
                      @click="updateStatus(group.id, 'approve')"
                    >
                      Apstiprināt
                    </v-btn>
                    <v-btn
                      color="error"
                      variant="tonal"
                      prepend-icon="mdi-close"
                      :loading="processingId === group.id && processingAction === 'decline'"
                      :disabled="!!processingId"
                      @click="updateStatus(group.id, 'decline')"
                    >
                      Noraidīt
                    </v-btn>
                    <v-spacer />

                    <v-btn
                      v-if="group.approval_url"
                      icon="mdi-file-document-outline"
                      variant="text"
                      color="primary"
                      aria-label="Atvērt dokumentu"
                      title="Atvērt dokumentu"
                      :href="group.approval_url"
                      target="_blank"
                      rel="noopener noreferrer"
                    />
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>

            <v-card
              v-else-if="!loadingGroups"
              class="empty-card pa-8 text-center"
              elevation="0"
            >
                <v-icon color="success" size="32">
                  mdi-check-circle-outline
                </v-icon>

              <h3 class="text-small">
                Visi pieteikumi izskatīti!
              </h3>
            </v-card>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </section>
    <!-- Lietotāji un visi kolektīvi -->
    <v-row dense>
      <!-- Lietotāji -->
      <v-col cols="12" lg="5">
        <v-card class="admin-card h-100" elevation="0">
          <v-card-title class="pa-5">
            <div class="d-flex align-center ga-3">
              <v-avatar color="blue-lighten-5">
                <v-icon color="primary">mdi-account-multiple</v-icon>
              </v-avatar>

              <div>
                <div class="text-h6 font-weight-bold">
                  Lietotāji
                </div>
                <div class="text-caption text-medium-emphasis">
                  Reģistrētie sistēmas lietotāji
                </div>
              </div>

              <v-spacer />

              <v-chip size="small" variant="tonal">
                {{ users.length }}
              </v-chip>
            </div>
          </v-card-title>

          <v-divider />

          <v-card-text class="pa-3">
            <v-text-field
              v-model="userSearch"
              placeholder="Meklēt pēc e-pasta..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              class="mb-3"
            />

            <v-list v-if="filteredUsers.length" class="pa-0">
              <v-list-item
                v-for="user in filteredUsers"
                :key="user.id"
                rounded="lg"
                class="mb-1"
              >
                <template #prepend>
                  <v-avatar color="blue-lighten-5" size="42">
                    <v-icon color="primary">mdi-account-outline</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title class="text-body-2 font-weight-medium">
                  {{ user.email }}
                </v-list-item-title>

                <v-list-item-subtitle>
                  {{ roleMap[user.role] || user.role }}
                </v-list-item-subtitle>

                <template #append>
                  <v-chip
                    :color="user.role === 'admin' ? 'primary' : 'default'"
                    size="x-small"
                    variant="tonal"
                  >
                    {{ roleMap[user.role] || user.role }}
                  </v-chip>
                </template>
              </v-list-item>
            </v-list>

            <div v-else class="text-center text-medium-emphasis pa-6">
              Lietotāji nav atrasti.
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Visi deju kolektīvi -->
      <v-col cols="12" lg="7">
        <v-card class="admin-card h-100" elevation="0">
          <v-card-title class="pa-5">
            <div class="d-flex align-center ga-3">
              <v-avatar color="purple-lighten-5">
                <v-icon color="purple-darken-1">
                  mdi-dance-ballroom
                </v-icon>
              </v-avatar>

              <div>
                <div class="text-h6 font-weight-bold">
                  Deju kolektīvi
                </div>
                <div class="text-caption text-medium-emphasis">
                  Visi reģistrētie kolektīvi
                </div>
              </div>

              <v-spacer />

              <v-chip size="small" variant="tonal">
                {{ allGroups.length }}
              </v-chip>
            </div>
          </v-card-title>

          <v-divider />

          <v-card-text class="pa-3">
            <v-text-field
              v-model="groupSearch"
              placeholder="Meklēt kolektīvu..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              density="compact"
              hide-details
              clearable
              class="mb-3"
            />

            <v-list v-if="filteredGroups.length" class="pa-0">
              <v-list-item
                v-for="group in filteredGroups"
                :key="group.id"
                rounded="lg"
                class="mb-2 group-list-item"
              >
                <template #prepend>
                  <v-avatar color="purple-lighten-5" rounded="lg">
                    <v-icon color="purple-darken-1">
                      mdi-account-group
                    </v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title class="font-weight-bold">
                  {{ group.name }}
                </v-list-item-title>

                <v-list-item-subtitle class="mt-1">
                  <template v-if="group.leaders?.length">
                    Vadītāji:
                    {{ group.leaders.map(leader =>
                      `${leader.user?.name || ''} ${leader.user?.surname || ''}`.trim()
                    ).filter(Boolean).join(', ') }}
                  </template>
                  <template v-else>
                    Vadītāja informācija nav pieejama
                  </template>
                </v-list-item-subtitle>

                <div
                  v-if="group.age_groups?.length"
                  class="d-flex flex-wrap ga-1 mt-2"
                >
                  <v-chip
                    v-for="ageGroup in group.age_groups"
                    :key="ageGroup.id"
                    size="x-small"
                    variant="tonal"
                    color="primary"
                  >
                    {{ ageGroup.name }}
                    <template v-if="ageGroup.age_group">
                      ({{ ageGroup.age_group }})
                    </template>
                  </v-chip>
                </div>

                <template #append>
                  <v-btn
                    icon="mdi-delete-outline"
                    variant="text"
                    color="error"
                    aria-label="Dzēst kolektīvu"
                    title="Dzēst kolektīvu"
                    :loading="deletingId === group.id"
                    :disabled="!!deletingId"
                    @click.stop="deleteDanceGroups(group.id)"
                  />
                </template>
              </v-list-item>
            </v-list>

            <div v-else class="text-center text-medium-emphasis pa-6">
              Kolektīvi nav atrasti.
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <!-- Kolektīva informācijas logs -->
    <AdminDanceGroupDialog
      v-model="groupInfoDialog"
      :group="selectedGroup"
    />
    <!-- Dzēšanas apstiprinājums -->
    <v-dialog v-model="deleteDialog" max-width="420">
      <v-card rounded="xl">
        <v-card-title class="pa-5">
          Dzēst kolektīvu?
        </v-card-title>

        <v-card-text>
          Vai tiešām vēlies dzēst kolektīvu
          <strong>{{ groupToDelete?.name }}</strong>?
          Šo darbību var nebūt iespējams atsaukt.
        </v-card-text>

        <v-card-actions class="pa-4">
          <v-spacer />

          <v-btn
            variant="text"
            :disabled="!!deletingId"
            @click="deleteDialog = false"
          >
            Atcelt
          </v-btn>

          <v-btn
            color="error"
            variant="tonal"
            :loading="!!deletingId"
            @click="confirmDeleteGroup"
          >
            Dzēst
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Darbību paziņojums -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3500"
      location="bottom end"
    >
      {{ snackbar.message }}

      <template #actions>
        <v-btn
          variant="text"
          @click="snackbar.show = false"
        >
          Aizvērt
        </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script>
import axios from 'axios'
import AdminDanceGroupDialog from '../admin/AdminShowDanceGroup.vue'

export default {
  components: {
      AdminDanceGroupDialog
  },
  data() {
    return {
      groups: [],
      users: [],
      allGroups: [],

      selectedGroup: null,
      groupInfoDialog: false,

      userSearch: '',
      groupSearch: '',

      loading: false,
      loadingGroups: false,
      processingId: null,
      processingAction: null,
      deletingId: null,

      deleteDialog: false,
      groupToDelete: null,

      pageError: '',

      snackbar: {
        show: false,
        message: '',
        color: 'success'
      },

      statusMap: {
        waiting: 'Gaida apstiprinājumu',
        approved: 'Apstiprināts',
        declined: 'Noraidīts'
      },

      roleMap: {
        user: 'Lietotājs',
        admin: 'Administrators'
      }
    }
  },

  computed: {
    filteredUsers() {
      const search = this.userSearch.trim().toLowerCase()

      if (!search) return this.users

      return this.users.filter(user =>
        (user.email || '').toLowerCase().includes(search)
      )
    },

    filteredGroups() {
      const search = this.groupSearch.trim().toLowerCase()

      if (!search) return this.allGroups

      return this.allGroups.filter(group =>
        (group.name || '').toLowerCase().includes(search)
      )
    }
  },

  mounted() {
    this.refreshData()
  },

  methods: {
    openGroupInfo(group) {
      this.selectedGroup = group
      this.groupInfoDialog = true
    },
    async refreshData() {
      this.loading = true
      this.pageError = ''

      try {
        await Promise.all([
          this.fetchGroups(),
          this.fetchUsers(),
          this.fetchAllGroups()
        ])
      } catch (error) {
        this.pageError = 'Neizdevās ielādēt visus datus. Mēģini vēlreiz.'
      } finally {
        this.loading = false
      }
    },

    async fetchGroups() {
      this.loadingGroups = true

      try {
        const response = await axios.get(
          '/api/admin/danceGroups',
          { withCredentials: true }
        )

        this.groups = response.data.data || []

      } catch (error) {
        console.error('Neizdevās ielādēt apstiprinājumus:', error)
        throw error
      } finally {
        this.loadingGroups = false
      }
    },

    async fetchUsers() {
      try {
        const response = await axios.get(
          '/api/admin/users',
          { withCredentials: true }
        )

        this.users = response.data.data || []
      } catch (error) {
        console.error('Neizdevās ielādēt lietotājus:', error)
        throw error
      }
    },

    async fetchAllGroups() {
      try {
        const response = await axios.get(
          '/api/danceGroups-all',
          { withCredentials: true }
        )

        this.allGroups = response.data.data || []
      } catch (error) {
        console.error('Neizdevās ielādēt kolektīvus:', error)
        throw error
      }
    },

    async updateStatus(id, action) {
      if (this.processingId) return

      const url = action === 'approve'
        ? '/api/admin/danceGroups/approval'
        : '/api/admin/danceGroups/decline'

      this.processingId = id
      this.processingAction = action

      try {
        await axios.post(
          url,
          { id },
          { withCredentials: true }
        )

        this.groups = this.groups.filter(group => group.id !== id)

        this.showMessage(
          action === 'approve'
            ? 'Kolektīvs veiksmīgi apstiprināts!'
            : 'Kolektīvs noraidīts.',
          'success'
        )

        await this.fetchAllGroups()
      } catch (error) {
        console.error('Neizdevās mainīt kolektīva statusu:', error)

        this.showMessage(
          error.response?.data?.message ||
            'Neizdevās atjaunināt kolektīva statusu.',
          'error'
        )
      } finally {
        this.processingId = null
        this.processingAction = null
      }
    },

    deleteDanceGroups(id) {
      this.groupToDelete =
        this.allGroups.find(group => group.id === id) || { id }

      this.deleteDialog = true
    },

    async confirmDeleteGroup() {
      if (!this.groupToDelete || this.deletingId) return

      const id = this.groupToDelete.id

      this.deletingId = id

      try {
        await axios.delete(
          `/api/danceGroups/${id}`,
          { withCredentials: true }
        )

        this.allGroups = this.allGroups.filter(group => group.id !== id)

        this.deleteDialog = false
        this.groupToDelete = null

        this.showMessage('Kolektīvs veiksmīgi dzēsts.', 'success')
      } catch (error) {
        console.error('Neizdevās dzēst kolektīvu:', error)

        this.showMessage(
          error.response?.data?.message ||
            'Neizdevās dzēst kolektīvu.',
          'error'
        )
      } finally {
        this.deletingId = null
      }
    },

    openGroupInfo(group) {
      this.selectedGroup = group
      this.groupInfoDialog = true
    },

    statusColor(status) {
      const colors = {
        waiting: 'orange-darken-2',
        approved: 'success',
        declined: 'error'
      }

      return colors[status] || 'grey'
    },

    showMessage(message, color = 'success') {
      this.snackbar = {
        show: true,
        message,
        color
      }
    }
  }
}
</script>