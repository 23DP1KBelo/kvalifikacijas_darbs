<template>
  <v-container class="d-flex flex-column align-center py-8">
    <h1 class="text-center mb-8 text-primary">Dejotāju apstiprināšana</h1>

    <v-row class="w-100" justify="center">
    <v-col
      v-for="dancer in dancers"
      :key="dancer.id"
      cols="12"
      sm="10"
      md="6"
      lg="5"
      xl="4"
      class="d-flex justify-center"
    >
      <v-card
        class="mb-8 rounded-xl elevation-6 pa-2 dancer-card"
        width="100%"
        max-width="500"
      >
        <!-- Card header -->
        <v-card-title class="bg-softblue text-primary font-weight-bold text-h6 text-md-h5 rounded-t-xl">
          {{ dancer.dance_group.name || 'Kolektīvs bez nosaukuma' }}
        </v-card-title>

        <!-- Card content -->
        <v-card-text class="d-flex flex-column gap-3 px-6 py-6">
          <div>
            <strong>Vārds:</strong>
            {{ dancer.user.name }} {{ dancer.user.surname }}
          </div>

          <div>
            <strong>Loma:</strong>
            {{ translateRole(dancer.role) }}
          </div>

          <div>
            <strong>Vecuma grupa:</strong>
            <v-select
              class="mt-2 w-100"
              label="Izvēlies grupu"
              :items="ageGroups"
              item-title="name"
              item-value="id"
              v-model="dancer.selectAgeGroup"
              variant="outlined"
              density="comfortable"
            />
          </div>
        </v-card-text>
        <v-alert v-if="dancer.error" type="error" dense outlined class="mt-3">
            {{ dancer.error }}
        </v-alert>

        <!-- Card actions -->
        <v-card-actions class="d-flex justify-end px-6 pb-4">
          <v-btn
            class="mr-3"
            color="green"
            variant="elevated"
            @click="updateStatus(dancer, 'approve')"
          >
            Apstiprināt
          </v-btn>

          <v-btn
            color="red"
            variant="outlined"
            @click="updateStatus(dancer, 'decline')"
          >
            Noraidīt
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>

    <v-alert v-if="dancers.length === 0" type="info" class="mt-4 w-75 text-center">
      Nav dejotāju, kuri gaida apstiprinājumu.
    </v-alert>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      dancers: [],
      group: {},
      roleMap: {
        leader: "Vadītājs",
        dancer: "Dejotājs",
        admin: "Administrators",
      },
      ageGroups: [],
    };
  },
  watch: {
    "$route.params.id"(newId) {
      this.group.id = newId;
      this.fetchDancers();
    },
  },
  mounted() {
    this.group.id = this.$route.params.id;
    this.fetchDanceGroup();
    this.fetchDancers();
  },
  methods: {
    fetchDancers() {
      axios
        .get(`/api/dancers/${this.group.id}`, { withCredentials: true })
        .then((res) => {
          this.dancers = (res.data.data || []).map(dancer => ({
            ...dancer,
            selectAgeGroup: null,
            error: '' 
          }));
        })
        .catch((err) => console.log(err));
    },
    fetchDanceGroup(){
      const groupId = this.$route.params.id;
      if (!groupId) return;

      axios
        .get(`/api/dance-group-info/${groupId}`)
        .then((res) => {
          this.group = res.data.data;
          this.ageGroups = this.group.age_groups || [];
          console.log(this.ageGroups)
        })
        .catch((err) => console.error(err));
    },
    updateStatus(dancer, action) {
      const url = action === "approve" ? "/api/dancers/approval" : "/api/dancers/decline";

      if(!dancer.selectAgeGroup ) {
        dancer.error = 'Lūdzu, izvēlieties vecuma grupu pirms apstiprināšanas.';
        return;
      }
      axios
        .post(
          url,
          {
            id: dancer.id,
            role: dancer.role,
            user_id: dancer.user.id,
            age_group_id: dancer.selectAgeGroup,
            dance_group_id: dancer.dance_group.id,
          },
          { withCredentials: true }
        )
        .then(() => {
          this.dancers = this.dancers.filter((d) => d.id !== dancer.id);
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
.dancer-card {
  transition: all 0.25s ease;
}

.dancer-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.18);
}
</style>
