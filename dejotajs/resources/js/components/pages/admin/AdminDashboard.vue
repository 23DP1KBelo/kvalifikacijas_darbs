<template>
  <v-container fluid class="py-8">

    <h1 class="text-center mb-10 text-primary font-weight-bold">
      Deju kolektīvu apstiprinājumi
    </h1>

    <v-row dense class="pa-2">
      <v-col
        v-for="group in groups"
        :key="group.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        class="d-flex"
      >
        <v-card class="flex-grow-1 mb-4 rounded-lg elevation-4 group-card d-flex flex-column">

          <v-card-title class="bg-softblue text-primary font-weight-bold d-flex justify-space-between align-center">
            {{ group.name }}

            <v-btn size="small" color="primary" variant="text" @click="openGroupInfo(group)">
              Vairāk
            </v-btn>
          </v-card-title>

          <v-card-text class="px-4 py-4 bg-secondary flex-grow-1">
            <div>
              <strong>Status:</strong> {{ statusMap[group.status] || group.status }}
            </div>
          </v-card-text>

          <v-card-actions class="px-4 py-3 bg-secondary flex-wrap justify-end">
            <v-btn
              color="green"
              variant="tonal"
              size="small"
              class="ma-1"
              @click="updateStatus(group.id, 'approve')"
            >
              Apstiprināt
            </v-btn>

            <v-btn
              color="red"
              variant="tonal"
              size="small"
              class="ma-1"
              @click="updateStatus(group.id, 'decline')"
            >
              Noraidīt
            </v-btn>

            <v-btn
              v-if="group.approval_url"
              :href="group.approval_url"
              target="_blank"
              size="small"
              variant="outlined"
              class="ma-1"
            >
              Atvērt dokumentu
            </v-btn>
          </v-card-actions>

        </v-card>
      </v-col>
    </v-row>

    <v-alert
      v-if="groups.length === 0"
      type="info"
      class="mt-4 text-center"
    >
      Nav atrasti kolektīvi, kam nepieciešams apstiprinājums.
    </v-alert>

    <v-row class="mt-12" dense>

      <v-col cols="12" md="6">
        <h2 class="text-center mb-4 text-primary">Lietotāji</h2>

        <v-list>
          <v-list-item
            v-for="user in users"
            :key="user.id"
            class="bg-secondary mb-2 rounded-lg px-4 py-2"
          >
            <v-list-item-title>{{ user.email }}</v-list-item-title>
            <v-list-item-subtitle>
              {{ roleMap[user.role] || user.role }}
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </v-col>

      <v-col cols="12" md="6">
        <h2 class="text-center mb-4 text-primary">Deju kolektīvi</h2>

        <v-list>
          <v-list-item
            v-for="g in allGroups"
            :key="g.id"
            class="bg-secondary mb-2 rounded-lg px-4"
          >
            <v-list-item-content>

              <v-card-title class="d-flex justify-space-between align-center">
                {{ g.name }}

                <v-btn
                  icon="mdi-delete"
                  variant="text"
                  color="red"
                  @click="deleteDanceGroups(g.id)"
                />
              </v-card-title>

              <v-card-text class="py-0">
                <div v-for="leader in g.leaders" :key="leader.id">
                  {{ leader.user.name }} {{ leader.user.surname }}
                </div>
              </v-card-text>

              <v-card-subtitle v-for="ageGroup in g.age_groups" :key="ageGroup.id">
                {{ ageGroup.name }} ({{ ageGroup.age_group }})
              </v-card-subtitle>

            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-col>

    </v-row>

    <v-dialog v-model="groupInfoDialog" max-width="600px">
      <v-card v-if="selectedGroup">

        <v-card-title>
          {{ selectedGroup.name }}
        </v-card-title>

        <v-card-text>

          <p><strong>Apraksts:</strong> {{ selectedGroup.description }}</p>

          <p><strong>Status:</strong> {{ statusMap[selectedGroup.status] }}</p>

          <p>
            <strong>Adrese:</strong>
            {{ selectedGroup.city + ' ' + selectedGroup.address }}
          </p>

          <a
            v-if="selectedGroup.approval_url"
            :href="selectedGroup.approval_url"
            target="_blank"
          >
            Atvērt dokumentu
          </a>

        </v-card-text>

        <v-card-actions>
          <v-btn text @click="groupInfoDialog = false">
            Aizvērt
          </v-btn>
        </v-card-actions>

      </v-card>
    </v-dialog>

  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      groups: [],
      users: [],
      allGroups: [],
      selectedGroup: null,
      groupInfoDialog: false,
      statusMap: {
        waiting: 'Gaida apstiprinājumu',
        approved: 'Apstiprināts',
        declined: 'Noraidīts'
      },
      roleMap: {
        user: 'Lietotājs',
        admin: 'Administrators'
      }
    };
  },

  mounted() {
    this.fetchGroups();
    this.fetchUsers();
    this.fetchAllGroups();
  },

  methods: {
    fetchGroups() {
      axios.get('/api/admin/danceGroups', { withCredentials: true })
        .then(res => this.groups = res.data.data || [])
        .catch(console.error);
    },

    fetchUsers() {
      axios.get('/api/admin/users', { withCredentials: true })
        .then(res => this.users = res.data.data || [])
        .catch(console.error);
    },

    fetchAllGroups() {
      axios.get('/api/danceGroups-all')
        .then(res => this.allGroups = res.data.data || [])
        .catch(console.error);
    },

    updateStatus(id, action) {
      const url = action === 'approve'
        ? '/api/admin/danceGroups/approval'
        : '/api/admin/danceGroups/decline';

      axios.post(url, { id }, { withCredentials: true })
        .then(() => {
          this.groups = this.groups.filter(g => g.id !== id);
        })
        .catch(console.error);
    },

    deleteDanceGroups(id) {
      if (confirm('Vai tiešām vēlaties dzēst šo kolektīvu?')) {
        axios.delete(`/api/danceGroups/${id}`, { withCredentials: true })
          .then(() => this.fetchAllGroups())
          .catch(console.error);
      }
    },

    openGroupInfo(group) {
      this.selectedGroup = group;
      this.groupInfoDialog = true;
    }
  }
};
</script>

<style>
.v-card {
  transition: transform 0.2s, box-shadow 0.2s;
}

.v-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

.group-card {
  min-height: 140px;
}

h1, h2 {
  font-weight: 700;
}

.v-list-item:hover {
  background-color: rgba(0,123,255,0.1);
}
</style>