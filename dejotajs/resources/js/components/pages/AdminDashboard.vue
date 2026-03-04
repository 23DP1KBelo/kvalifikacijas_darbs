<template>
  <v-container class="d-flex flex-column align-center py-8">

    <h1 class="text-center mb-8 text-primary">Deju kolektīvu apstiprinājumi</h1>

    <!-- Grupas kartes kolonnā -->
    <v-row class="w-100" justify="center" dense>
      <v-col
        v-for="group in groups"
        :key="group.id"
        cols="12"
        sm="10"
        md="8"
      >
        <v-card class="mb-6 rounded-lg elevation-4 group-card">
          <!-- Card header -->
          <v-card-title class="bg-softblue text-primary font-weight-bold rounded-t-lg">
            {{ group.name }}
          </v-card-title>

          <!-- Card content -->
          <v-card-text class="d-flex flex-column gap-2 px-4 py-4 bg-secondary">
            <div>
              <strong>Statuss:</strong> {{ statusMap[group.status] || group.status }}
            </div>
          </v-card-text>

          <!-- Card actions -->
          <v-card-actions class="d-flex justify-end px-4 py-2 bg-secondary">
            <v-btn
              class="mx-2"
              color="green"
              variant="tonal"
              small
              @click="updateStatus(group.id, 'approve')"
            >
              Apstiprināt
            </v-btn>
            <v-btn
              color="red"
              variant="tonal"
              small
              @click="updateStatus(group.id, 'decline')"
            >
              Noraidīt
            </v-btn>
            <v-btn
              v-if="group.approval_url"
              :href="group.approval_url"
              target="_blank"
              small
              variant="outlined"
              class="ml-2"
            >
              Atvērt dokumentu
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Nav grupu -->
    <v-alert
      v-if="groups.length === 0"
      type="info"
      class="mt-4 w-75 text-center"
    >
      Nav atrasti kolektīvi, kam nepieciešams apstiprinājums.
    </v-alert>

    <!-- Lietotāji saraksts -->
    <h1 class="text-center mb-4 text-primary mt-12">Lietotāji</h1>
    <v-list class="w-100" style="max-width: 600px;">
      <v-list-item v-for="user in users" :key="user.id" class="bg-secondary mb-2 rounded-lg">
        <v-list-item-content>
          <v-list-item-title>{{ user.email }}</v-list-item-title>
          <v-list-item-subtitle>{{ roleMap[user.role] || user.role }}</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      groups: [],
      users: [],
      statusMap: {
        pending: 'Gaidīšana',
        approved: 'Apstiprināts',
        declined: 'Noraidīts'
      },
      roleMap: {
        leader: 'Vadītājs',
        dancer: 'Dejotājs',
        admin: 'Administrators'
      }
    };
  },
  mounted() {
    this.fetchGroups();
    this.fetchUsers();
  },
  methods: {
    fetchGroups() {
      axios.get('/admin/danceGroups', { withCredentials: true })
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : [];
        })
        .catch(err => console.log(err));
    },
    fetchUsers(){
      axios.get('/admin/users', {withCredentials: true})
        .then(res => {
          this.users = Array.isArray(res.data.data) ? res.data.data : [];
        })
        .catch(err => console.log(err));
    },
    updateStatus(id, action) {
      const url = action === 'approve'
        ? '/admin/danceGroups/approval'
        : '/admin/danceGroups/decline';

      axios.post(url, { id }, { withCredentials: true })
        .then(() => {
          this.groups = this.groups.filter(g => g.id !== id);
        })
        .catch(err => {
          console.error(err);
        });
    }
  }
}
</script>

<style>
/* Kartes hover efekts */
.v-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.v-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

/* Grupas kartes platums un augstums */
.group-card {
  min-height: 120px;   /* ne pārāk garas */
  width: 100%;
}
</style>
