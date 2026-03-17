<template>
  <v-container class="py-8">
    <h1 class="text-center mb-8 text-primary">Lietotāja profils</h1>

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
                    Vadītājs
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
import axios from 'axios';

export default {
  data() {
    return {
      user: {
        name: '',
        surname: '',
        email: '',
        dance_groups: []
      },
      error: ''
    }
  },
  computed: {
    approvedGroups() {
      return this.user.dance_groups.filter(g => g.status === 'approved');
    }
  },
  async mounted() {
    try {
      const res = await axios.get('api/profile', { withCredentials: true });

      this.user.name = res.data.user.name;
      this.user.surname = res.data.user.surname;
      this.user.email = res.data.user.email;

      this.user.dance_groups = res.data.dance_group_members.map(member => ({
        id: member.dance_group.id,
        name: member.dance_group.name,
        role: member.role,
        age_group: member.age_group?.age_group || null,
        status: member.status 
      }));
    } catch (err) {
      this.error = 'Neizdevās ielādēt profilu';
      console.error(err);
    }
  }
}
</script>


