
<template>
  <v-container fluid class="pa-4 pa-md-8">
    <!-- Lapas galvene -->
    <div class="mb-8">
      <h1>ADMINISTRĒŠANA</h1>
      <p class="text-body-2 text-small mt-2">
        Pārvaldi pieteikumus, lietotājus, kolektīvus, pasākumus un ierakstus.
      </p>
    </div>

    <!-- Kopsavilkuma kartītes -->
    <v-row dense class="mb-8">
      <v-col cols="12" sm="3">
        <v-card class="pa-4" elevation="1">
          <div class="d-flex align-center ga-5 mt-5">
            <v-icon color="blue-darken-2">mdi-account-group-outline</v-icon>
            <div class="ml-6">
              <div class="text-h5 font-weight-bold">
                Lietotāji {{ userTotal }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="3">
        <v-card class="pa-4" elevation="1">
          <div class="d-flex align-center ga-5 mt-5">
            <v-icon color="green-darken-2">mdi-dance-ballroom</v-icon>
            <div class="ml-6">
              <div class="text-h5 font-weight-bold">
                Kolektīvi {{ groupTotal }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="2">
        <v-card class="pa-4" elevation="1">
          <div class="d-flex align-center ga-2 mt-5">
            <v-icon color="yellow-darken-2">mdi-text-box-multiple</v-icon>
            <div class="ml-6">
              <div class="text-h6 font-weight-bold">
                Ieraksti {{ postTotal }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="2">
        <v-card class="pa-4" elevation="1">
          <div class="d-flex align-center ga-2 mt-5">
            <v-icon color="blue-darken-2">mdi-calendar-month</v-icon>
            <div class="ml-6">
              <div class="text-h6 font-weight-bold">
                Pasākumi {{ eventTotal }}
              </div>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Lapas kļūda -->
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
              <h2 class="text-small">Apstiprinājumi</h2>
              <v-chip color="red-darken-2" variant="tonal">
                {{ groups.length }}
              </v-chip>
            </div>
          </v-expansion-panel-title>

          <v-expansion-panel-text>
            <v-progress-linear
              v-if="loadingGroups"
              indeterminate
              color="accent"
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
                      {{ group.city || 'Pilsēta nav norādīta' }}
                      <span v-if="group.address">
                        , {{ group.address }}
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
                      :loading="processingId === group.id && processingAction === 'approve'"
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
                      color="accent"
                      aria-label="Atvērt dokumentu"
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
              <h3 class="text-small">Visi pieteikumi izskatīti!</h3>
            </v-card>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
    </section>

    <!-- Navigācija: lietotāji, kolektīvi, ieraksti, pasākumi -->
    <v-card class="admin-card mt-6" elevation="0">
      <v-tabs
        v-model="activeSection"
        color="accent"
        class="text-small"
        show-arrows
        @update:model-value="changeSection"
      >
        <v-tab value="users" prepend-icon="mdi-account-multiple">
          Lietotāji
        </v-tab>
        <v-tab value="groups" prepend-icon="mdi-dance-ballroom">
          Kolektīvi
        </v-tab>
        <v-tab value="posts" prepend-icon="mdi-text-box-multiple">
          Ieraksti
        </v-tab>
        <v-tab value="events" prepend-icon="mdi-calendar-month">
          Pasākumi
        </v-tab>
      </v-tabs>

      <v-divider />

      <v-card-text class="pa-4">
        <div class="d-flex flex-wrap align-center ga-3 mb-4">
          <div>
            <div class="text-h6 font-weight-bold">{{ activeTitle }}</div>
            <div class="text-caption text-medium-emphasis">
              {{ activeSubtitle }}
            </div>
          </div>
        </div>
        <!-- Meklēšana -->
        <v-text-field
          v-model="searchQuery"
          :placeholder="searchPlaceholder"
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          density="compact"
          hide-details
          clearable
          class="mb-4"
        />

        <v-alert
          v-if="searchError"
          type="error"
          variant="tonal"
          closable
          class="mb-4"
          @click:close="searchError = ''"
        >
          {{ searchError }}
        </v-alert>

        <!-- Lietotāji -->
        <v-data-table-server
          v-if="activeSection === 'users'"
          :headers="userHeaders"
          :items="activeItems"
          :items-length="totalItems"
          :loading="searchLoading"
          :page="page"
          :items-per-page="perPage"
          :items-per-page-options="[5, 10, 25, 50]"
          item-value="id"
          no-data-text="Lietotāji nav atrasti."
          loading-text="Ielādē lietotājus..."
          items-per-page-text="Ieraksti lapā"
          @update:options="onTableOptions"
        >
          <template v-slot:[`item.email`]="{ item }">
            <div class="d-flex align-center ga-3 py-1">
                <v-icon color="accent">mdi-account-outline</v-icon>
              <p>
                {{ item.name }} {{ item.surname }} ({{ item.email }})
              </p>
            </div>
          </template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              color="error"
              aria-label="Dzēst lietotāju"
              :loading="deletingId === item.id && deletingType === 'users'"
              :disabled="!!deletingId"
              @click="deleteItem('users', item.id, item.email)"
            />
          </template>

          <template v-slot:[`item.role`]="{ item }">
            <v-chip
              :color="item.role === 'admin' ? 'accent' : 'default'"
              size="small"
            >
              {{ roleMap[item.role] || item.role }}
            </v-chip>
          </template>
        </v-data-table-server>

        <!-- Kolektīvi -->
        <v-data-table-server
          v-else-if="activeSection === 'groups'"
          :headers="groupHeaders"
          :items="activeItems"
          :items-length="totalItems"
          :loading="searchLoading"
          :page="page"
          :items-per-page="perPage"
          :items-per-page-options="[5, 10, 25, 50]"
          item-value="id"
          no-data-text="Kolektīvi nav atrasti."
          loading-text="Ielādē kolektīvus..."
          items-per-page-text="Ieraksti lapā"
          @update:options="onTableOptions"
        >
          <template v-slot:[`item.name`]="{ item }">
            <div class="py-2">
              <div class="font-weight-bold">{{ item.name }}</div>
              <div class="text-caption text-medium-emphasis">
                {{ item.city || 'Pilsēta nav norādīta' }}
              </div>

              <div
                v-if="item.age_groups?.length"
                class="d-flex flex-wrap ga-1 mt-2"
              >
                <v-chip
                  v-for="ageGroup in item.age_groups"
                  :key="ageGroup.id"
                  size="x-small"
                  color="accent"
                  variant="tonal"
                >
                  {{ ageGroup.name }}
                </v-chip>
              </div>
            </div>
          </template>

          <template v-slot:[`item.leaders`]="{ item }">
            {{
              item.leaders?.map(leader =>
                `${leader.user?.name || ''} ${leader.user?.surname || ''}`.trim()
              ).filter(Boolean).join(', ') || 'Vadītāja informācija nav pieejama'
            }}
          </template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              color="error"
              aria-label="Dzēst kolektīvu"
              :loading="deletingId === item.id && deletingType === 'groups'"
              :disabled="!!deletingId"
              @click="deleteItem('groups', item.id, item.name)"
            />
          </template>
        </v-data-table-server>

        <!-- Ieraksti -->
        <v-data-table-server
          v-else-if="activeSection === 'posts'"
          :headers="postHeaders"
          :items="activeItems"
          :items-length="totalItems"
          :loading="searchLoading"
          :page="page"
          :items-per-page="perPage"
          :items-per-page-options="[5, 10, 25, 50]"
          item-value="id"
          no-data-text="Ieraksti nav atrasti."
          loading-text="Ielādē ierakstus..."
          items-per-page-text="Ieraksti lapā"
          @update:options="onTableOptions"
        >

          <template v-slot:[`item.title`]="{ item }">
            <div class="py-2" style="white-space: normal; min-width: 180px">
              {{ item.title || 'Nav nosaukuma' }}
            </div>
          </template>

          <template v-slot:[`item.danceGroup`]="{ item }">
            {{ item.dance_group_member?.dance_group?.name || 'Nav norādīts' }}
          </template>

          <template v-slot:[`item.created_at`]="{ item }">
            {{ formatDate(item.created_at) }}
          </template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              color="error"
              aria-label="Dzēst ierakstu"
              :loading="deletingId === item.id && deletingType === 'posts'"
              :disabled="!!deletingId"
              @click="deleteItem('posts', item.id, item.title)"
            />
          </template>
        </v-data-table-server>

        <!-- Pasākumi -->
        <v-data-table-server
          v-else-if="activeSection === 'events'"
          :headers="eventHeaders"
          :items="activeItems"
          :items-length="totalItems"
          :loading="searchLoading"
          :page="page"
          :items-per-page="perPage"
          :items-per-page-options="[5, 10, 25, 50]"
          item-value="id"
          no-data-text="Pasākumi nav atrasti."
          loading-text="Ielādē pasākumus..."
          items-per-page-text="Ieraksti lapā"
          @update:options="onTableOptions"
        >
          <template v-slot:[`item.name`]="{ item }">
            <div class="py-2">
              <div class="font-weight-medium">
                {{ item.name || item.title || 'Bez nosaukuma' }}
              </div>
              <div
                v-if="item.location"
                class="text-caption text-medium-emphasis"
              >
                {{ [item.location].filter(Boolean).join(', ') }}
              </div>
            </div>
          </template>

          <template v-slot:[`item.danceGroup`]="{ item }">
            {{ item.dance_group_member?.dance_group || 'Nav norādīts' }}
          </template>


          <template v-slot:[`item.date`]="{ item }">
            {{ formatDate(item.date || item.start_date || item.created_at) }}
          </template>

          <template v-slot:[`item.actions`]="{ item }">
            <v-btn
              icon="mdi-delete-outline"
              variant="text"
              color="error"
              aria-label="Dzēst pasākumu"
              :loading="deletingId === item.id && deletingType === 'events'"
              :disabled="!!deletingId"
              @click="deleteItem('events', item.id, item.name || item.title)"
            />
          </template>
        </v-data-table-server>
      </v-card-text>
    </v-card>

    <!-- Kolektīva informācijas logs -->
    <AdminDanceGroupDialog
      v-model="groupInfoDialog"
      :group="selectedGroup"
    />

    <!-- Paziņojumi -->
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      timeout="3500"
      location="bottom end"
    >
      {{ snackbar.message }}

      <template v-slot:actions>
        <v-btn variant="text" @click="snackbar.show = false">
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
      allGroups: [],
      users: [],

      selectedGroup: null,
      groupInfoDialog: false,

      activeSection: 'users',
      activeItems: [],
      searchQuery: '',
      searchLoading: false,
      searchError: '',

      page: 1,
      perPage: 10,
      totalItems: 0,

      userTotal: 0,
      groupTotal: 0,
      postTotal: 0,
      eventTotal: 0,

      requestNumber: 0,
      searchTimer: null,

      searchEndpoints: {
        users: '/api/admin/users/search',
        groups: '/api/admin/danceGroups/search',
        posts: '/api/admin/posts/search',
        events: '/api/admin/events/search'
      },

      userHeaders: [
        { title: 'E-pasts', key: 'email', sortable: false },
        { title: 'Loma', key: 'role', sortable: false },
        { title: 'Darbības', key: 'actions', sortable: false, align: 'end' }
      ],

      groupHeaders: [
        { title: 'Kolektīvs', key: 'name', sortable: false },
        { title: 'Vadītāji', key: 'leaders', sortable: false },
        { title: 'Darbības', key: 'actions', sortable: false, align: 'end' }
      ],

      postHeaders: [
        { title: 'Ieraksts', key: 'title', sortable: false },
        { title: 'Kolektīvs', key:'danceGroup', sortable: false},
        { title: 'Izveidots', key: 'created_at', sortable: false },
        { title: 'Darbības', key: 'actions', sortable: false, align: 'end' }
      ],

      eventHeaders: [
        { title: 'Pasākums', key: 'name', sortable: false },
        { title: 'Kolektīvs', key:'danceGroup', sortable: false},
        { title: 'Datums', key: 'date', sortable: false },
        { title: 'Darbības', key: 'actions', sortable: false, align: 'end' }
      ],

      loading: false,
      loadingGroups: false,
      processingId: null,
      processingAction: null,
      deletingId: null,
      deletingType: null,

      pageError: '',

      snackbar: {
        show: false,
        message: '',
        color: 'success'
      },

      roleMap: {
        user: 'Lietotājs',
        admin: 'Administrators'
      }
    }
  },

  computed: {
    activeTitle() {
      const titles = {
        users: 'Lietotāji',
        groups: 'Deju kolektīvi',
        posts: 'Ieraksti',
        events: 'Pasākumi'
      }

      return titles[this.activeSection]
    },

    activeSubtitle() {
      const subtitles = {
        users: 'Reģistrētie sistēmas lietotāji',
        groups: 'Visi reģistrētie deju kolektīvi',
        posts: 'Visi publicētie ieraksti',
        events: 'Visi reģistrētie pasākumi'
      }

      return subtitles[this.activeSection]
    },

    searchPlaceholder() {
      const placeholders = {
        users: 'Meklēt pēc e-pasta...',
        groups: 'Meklēt kolektīvu...',
        posts: 'Meklēt ierakstu...',
        events: 'Meklēt pasākumu...'
      }

      return placeholders[this.activeSection]
    }
  },

  watch: {
    searchQuery() {
      clearTimeout(this.searchTimer)

      this.searchTimer = setTimeout(() => {
        this.page = 1
        this.loadActiveItems()
      }, 350)
    }
  },

  mounted() {
    this.refreshData()
    this.loadActiveItems()
  },

  beforeUnmount() {
    clearTimeout(this.searchTimer)
    this.requestNumber++
  },

  methods: {
    async refreshData() {
      this.loading = true
      this.pageError = ''

      try {
        await Promise.all([
          this.fetchGroups(),
          this.fetchAllGroups(),
          this.fetchSummaryCounts()
        ])
      } catch (error) {
        console.error('Neizdevās ielādēt administrēšanas datus:', error)
        this.pageError = 'Neizdevās ielādēt visus datus. Mēģini vēlreiz.'
      } finally {
        this.loading = false
      }
    },

    async fetchSummaryCounts() {
      const sections = [
        ['users', 'userTotal'],
        ['groups', 'groupTotal'],
        ['posts', 'postTotal'],
        ['events', 'eventTotal']
      ]

      const results = await Promise.allSettled(
        sections.map(([section]) =>
          axios.get(this.searchEndpoints[section], {
            params: { page: 1, per_page: 1 },
            withCredentials: true
          })
        )
      )

      results.forEach((result, index) => {
        if (result.status !== 'fulfilled') return

        const [, property] = sections[index]
        const data = result.value.data
        this[property] = data.meta?.total ?? data.total ?? 0
      })
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

    async fetchAllGroups() {
      try {
        const response = await axios.get(
          '/api/danceGroups-all',
          { withCredentials: true }
        )

        this.allGroups = response.data.data || []
        this.groupTotal = response.data.total ?? this.allGroups.length
      } catch (error) {
        console.error('Neizdevās ielādēt kolektīvus:', error)
        throw error
      }
    },

    async changeSection() {
      clearTimeout(this.searchTimer)

      this.page = 1
      this.totalItems = 0
      this.activeItems = []
      this.searchError = ''

      await this.loadActiveItems()
    },


    async onTableOptions(options) {
      const nextPage = Number(options.page) || 1
      const nextPerPage = Number(options.itemsPerPage) || 10

      const pageChanged = this.page !== nextPage
      const perPageChanged = this.perPage !== nextPerPage

      if (!pageChanged && !perPageChanged) return

      this.page = nextPerPage !== this.perPage ? 1 : nextPage
      this.perPage = nextPerPage

      await this.loadActiveItems()
    },


    async loadActiveItems() {
      const endpoint = this.searchEndpoints[this.activeSection]
      if (!endpoint) return

      const requestId = ++this.requestNumber
      const requestedSection = this.activeSection

      this.searchLoading = true
      this.searchError = ''

      try {
        const response = await axios.get(endpoint, {
          params: {
            q: this.searchQuery.trim(),
            page: this.page,
            per_page: this.perPage
          },
          withCredentials: true
        })

        if (
          requestId !== this.requestNumber ||
          requestedSection !== this.activeSection
        ) {
          return
        }

        const result = response.data
        const meta = result.meta || result

        this.activeItems = result.data || []
        this.page = meta.current_page || this.page
        this.perPage = meta.per_page || this.perPage
        this.totalItems = meta.total ?? 0

        if (requestedSection === 'users') this.userTotal = this.totalItems
        if (requestedSection === 'groups') this.groupTotal = this.totalItems
        if (requestedSection === 'posts') this.postTotal = this.totalItems
        if (requestedSection === 'events') this.eventTotal = this.totalItems

      } catch (error) {
        if (requestId !== this.requestNumber) return

        console.error('Neizdevās ielādēt meklēšanas rezultātus:', error)

        this.activeItems = []
        this.totalItems = 0
        this.searchError =
          error.response?.data?.message ||
          'Neizdevās ielādēt datus. Mēģini vēlreiz.'
      } finally {
        if (requestId === this.requestNumber) {
          this.searchLoading = false
        }
      }
    },

    openGroupInfo(group) {
      this.selectedGroup = group
      this.groupInfoDialog = true
    },

    async updateStatus(id, action) {
      if (this.processingId) return

      const url = action === 'approve'
        ? '/api/admin/danceGroups/approval'
        : '/api/admin/danceGroups/decline'

      this.processingId = id
      this.processingAction = action

      try {
        await axios.post(url, { id }, { withCredentials: true })

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

    async deleteItem(type, id, label = '') {
      if (!id || this.deletingId) return

      const endpoints = {
        users: `/api/admin/user/${id}`,
        groups: `/api/danceGroups/${id}`,
        posts: `/api/posts/admin/${id}`,
        events: `/api/event/admin/${id}`
      }

      const labels = {
        users: 'lietotāju',
        groups: 'kolektīvu',
        posts: 'ierakstu',
        events: 'pasākumu'
      }

      const endpoint = endpoints[type]
      if (!endpoint) {
        this.showMessage('Nezināms dzēšamā objekta veids.', 'error')
        return
      }

      const description = label ? ` \"${label}\"` : ''
      const confirmed = window.confirm(
        `Vai tiešām vēlies dzēst ${labels[type]}${description}? Šo darbību var nebūt iespējams atsaukt.`
      )
      if (!confirmed) return

      this.deletingId = id
      this.deletingType = type

      try {
        await axios.delete(endpoint, { withCredentials: true })

        if (type === 'groups') {
          this.allGroups = this.allGroups.filter(group => group.id !== id)
          this.groups = this.groups.filter(group => group.id !== id)
        }

        if (this.activeSection === type) {
          this.activeItems = this.activeItems.filter(item => item.id !== id)
          this.totalItems = Math.max(0, this.totalItems - 1)

          if (this.activeItems.length === 0 && this.page > 1) {
            this.page--
          }

          await this.loadActiveItems()
        }

        await this.fetchSummaryCounts()
        this.showMessage('Ieraksts veiksmīgi dzēsts.', 'success')
      } catch (error) {
        console.error(`Neizdevās dzēst: ${type}`, error)
        this.showMessage(
          error.response?.data?.message || 'Neizdevās izdzēst ierakstu.',
          'error'
        )
      } finally {
        this.deletingId = null
        this.deletingType = null
      }
    },

    formatDate(value) {
      if (!value) return '—'

      const date = new Date(value)
      if (Number.isNaN(date.getTime())) return String(value)

      return new Intl.DateTimeFormat('lv-LV', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      }).format(date)
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
