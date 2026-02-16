<template>
  <v-container class="d-flex flex-column">
    <v-container class="bg-secondary mx-0 mb-4" style="overflow-y: auto;">
      <v-card-title>Deju kolektīvu apstiprinājumi</v-card-title>
      <div v-if="groups.length > 0">
        <v-card v-for="group in groups" :key="group.id" class="mt-8 bg-primary">
          <v-card-title>{{ group.name }}</v-card-title>
          <v-card-subtitle>Statuss: {{ group.status }}</v-card-subtitle>
          <v-card-text>
            <v-btn
              v-if="group.approval_url"
              :href="group.approval_url"
              target="_blank"
              size="small"
            >
              Atvērt dokumentu
            </v-btn>
          </v-card-text>
          <v-card-actions class="d-flex justify-end">
            <v-btn color="green" size="x-small" @click="updateStatus(group.id, 'approve')">Apstiprināt</v-btn>
            <v-btn color="red" size="x-small" @click="updateStatus(group.id, 'decline')">Noraidīt</v-btn>
          </v-card-actions>
        </v-card>
      </div>
      <div v-else>
        <v-card-subtitle>Nav atrasti kolektīvi, kam nepieciešams apstiprinājums.</v-card-subtitle>
      </div>
    </v-container>

      <v-container class="bg-primary mx-0" style=" overflow-y: auto;">
        <v-card-title>Lietotāji:</v-card-title>
          <v-card v-for="user in users" :key="user.id" class="bg-secondary mt-4">
            <v-card-text>{{ user.email }}</v-card-text>
            <v-card-subtitle>{{ user.role }}</v-card-subtitle>
          </v-card>
      </v-container>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      groups: [],
      users: []
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
};
</script>

