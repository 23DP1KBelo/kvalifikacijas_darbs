<template>
  <v-container class="d-flex flex-column align-center py-8">
    <h1 class="text-center mb-8 text-primary">Vadītāju apstiprināšana</h1>

    <v-row class="w-100" justify="center">
      <v-col
        v-for="leader in leaders"
        :key="leader.id"
        cols="12"
        sm="8"
        md="6"
        lg="4"
      >
        <v-card class="mb-6 rounded-lg elevation-4">
          <!-- Card header -->
          <v-card-title class="bg-softblue text-primary font-weight-bold rounded-t-lg">
            {{ leader.dance_group.name || 'Kolektīvs bez nosaukuma' }}
          </v-card-title>

          <!-- Card content -->
          <v-card-text class="d-flex flex-column gap-2 px-4 py-4 bg-secondary">
            <div>
              <strong>Vārds:</strong> {{ leader.user.name }} {{ leader.user.surname }}
            </div>
            <div>
              <strong>Loma:</strong> {{ translateRole(leader.role) }}
            </div>
          </v-card-text>

          <!-- Card actions -->
          <v-card-actions class="d-flex justify-end px-4 py-2 bg-secondary">
            <v-btn
              class="mx-2"
              color="green"
              variant="tonal"
              small
              @click="updateStatus(leader, 'approve')"
            >
              Apstiprināt
            </v-btn>
            <v-btn
              color="red"
              variant="tonal"
              small
              @click="updateStatus(leader, 'decline')"
            >
              Noraidīt
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <v-alert v-if="leaders.length === 0" type="info" class="mt-4 w-75 text-center">
      Nav vadītāju, kuri gaida apstiprinājumu.
    </v-alert>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      leaders: [],
      group: {},
      roleMap: {
        leader: "Vadītājs",
        dancer: "Dejotājs",
        admin: "Administrators",
      },
    };
  },
  watch: {
    "$route.params.id"(newId) {
      this.group.id = newId;
      this.fetchLeaders();
    },
  },
  mounted() {
    this.group.id = this.$route.params.id;
    this.fetchLeaders();
  },
  methods: {
    fetchLeaders() {
      axios
        .get(`/approval-leader/${this.group.id}`, { withCredentials: true })
        .then((res) => {
          this.leaders = Array.isArray(res.data.data) ? res.data.data : [];
        })
        .catch((err) => console.log(err));
    },
    updateStatus(leader, action) {
      const url = action === "approve" ? "/approval-leader" : "/decline-leader";

      axios
        .post(
          url,
          {
            id: leader.id,
            role: leader.role,
            user_id: leader.user.id,
            dance_group_id: leader.dance_group.id,
          },
          { withCredentials: true }
        )
        .then(() => {
          this.leaders = this.leaders.filter((l) => l.id !== leader.id);
        })
        .catch((err) => console.error(err.response?.data || err));
    },
    translateRole(role) {
      return this.roleMap[role] || role;
    },
  },
};
</script>

<style>
/* Hover efekts uz kartēm */
.v-card {
  transition: transform 0.2s, box-shadow 0.2s;
}
.v-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}
</style>