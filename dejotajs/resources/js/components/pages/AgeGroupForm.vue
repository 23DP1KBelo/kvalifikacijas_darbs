<template>
     <v-container class="d-flex justify-center align-center mt-8">
        <v-card class=" bg-softblue" elevation="16" width="500px">
            <v-card-title class="text-center mb-4 mt-4">Grupas veidošana</v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field label="Nosaukums" v-model="name" required></v-text-field>
                    <v-text-field label="Vecuma grupa" v-model="age_group" required></v-text-field>
                    <v-alert v-if="error" type="error" dense outlined class="mt-3">
                        {{ error }}
                    </v-alert>
                    <div class="d-flex justify-center align-center text-center mb-4">
                        <v-btn class="mt-4" color="primary" @click="createAgeGroup()">
                            Izveidot
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
import axios from 'axios';
export default {
  props: ['danceGroupId'],
  data() {
    return {
      name: '',
      age_group: '',
      error: '',
      groupId: this.$route.params.id
    }
  },
  methods: {
    async createAgeGroup() {
      const groupId = this.$route.params.id;
      if (!groupId) return;

      try {
        await axios.post(`/api/ageGroups/create`, {
          name: this.name,
          age_group: this.age_group,
          dance_group_id: groupId
        }, {
          withCredentials: true,
          headers: { 'Content-Type': 'application/json' }
        });

        alert('Vecuma grupa veiksmīgi izveidota!');
        this.$router.push(`/group/${groupId}`);
      } catch (err) {
        this.error = err.response?.data?.message || 'Kļūda saglabājot vecuma grupu';
      }
    }
  }
}
</script>
