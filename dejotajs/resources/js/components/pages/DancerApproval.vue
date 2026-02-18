<template>
    <h1 class="text-center mt-8">Dejotāju apstiprināšana</h1>
    <v-container class="d-flex flex-column">
        <div class="bg-primary px-6 py-6">
            <v-card-title>Kolektīva nosaukums:</v-card-title>
            <v-card class="mb-4" v-for="dancer in dancers" :key="dancer.id">
                <v-card-title class="mt-4">{{ dancer.user.name }}</v-card-title>
                <v-card-title>{{ dancer.role }}</v-card-title>
                <div class="d-flex justify-end px-2 py-2">
                    <v-btn color="green" size="small" class="mx-2" @click="updateStatus(dancer, 'approve')">Apstiprināt</v-btn>
                    <v-btn color="red" size="small"  @click="updateStatus(dancer, 'decline')">Noraidīt</v-btn>
                </div>
            </v-card>
        </div>
    </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      dancers: [],
      group: {}
    };
  },
   watch: {
    '$route.params.id'(newId) {
      this.group.id = newId;
      this.fetchDancers(); 
    }
  },
  mounted() {
    this.group.id = this.$route.params.id;
    this.fetchDancers();
  },
  methods: {
    fetchDancers(){
       axios.get(`/dancers/${this.group.id}`, { withCredentials: true })
            .then(res => {
                this.dancers = Array.isArray(res.data.data) ? res.data.data : [];
                console.log(this.dancers)
            })
            .catch(err => console.log(err));
    },
   updateStatus(dancer, action) {
    const url = action === 'approve'
        ? '/dancers/approval'
        : '/dancers/decline';
    console.log(dancer)
    axios.post(url, {
        id: dancer.id,
        role: dancer.role,
        user_id: dancer.user.id,
        age_group_id: dancer.age_group.id,
        dance_group_id: dancer.dance_group.id
        }, { withCredentials: true })
        .then(() => {
        this.dancers = this.dancers.filter(d => d.id !== dancer.id);
        })
        .catch(err => {
        console.error(err.response?.data || err );
        });
    }
  }
};
</script>
