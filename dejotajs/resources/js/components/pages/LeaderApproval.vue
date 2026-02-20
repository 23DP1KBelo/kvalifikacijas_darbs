<template>
    <h1 class="text-center mt-8">Vadītāju apstiprināšana</h1>
    <v-container class="d-flex flex-column">
        <div class="bg-primary px-6 py-6">
            <v-card-title>Kolektīva nosaukums:</v-card-title>
            <v-card class="mb-4" v-for="leader in leaders" :key="leader.id">
                <v-card-title class="mt-4">{{ leader.user.name }}</v-card-title>
                <v-card-title>{{ leader.role }}</v-card-title>
                <div class="d-flex justify-end px-2 py-2">
                    <v-btn color="green" size="small" class="mx-2" @click="updateStatus(leader, 'approve')">Apstiprināt</v-btn>
                    <v-btn color="red" size="small"  @click="updateStatus(leader, 'decline')">Noraidīt</v-btn>
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
      leaders: [],
      group: {}
    };
  },
   watch: {
    '$route.params.id'(newId) {
      this.group.id = newId;
      this.fetchLeaders(); 
    }
  },
mounted() {
    this.group.id = this.$route.params.id;
    this.fetchLeaders();
},
 methods: {
  fetchLeaders() {
    axios.get(`/approval-leader/${this.group.id}`, { withCredentials: true })
      .then(res => {
        this.leaders = Array.isArray(res.data.data) ? res.data.data : [];
        console.log(this.leaders);
      })
      .catch(err => console.log(err));
  },
  updateStatus(leader, action) {
    const url = action === 'approve'
      ? '/approval-leader'
      : '/decline-leader';

    axios.post(url, {
        id: leader.id,
        role: leader.role,  
        user_id: leader.user.id,
        dance_group_id: leader.dance_group.id
    }, { withCredentials: true })
    .then(() => {
      this.leaders = this.leaders.filter(l => l.id !== leader.id);
    })
    .catch(err => console.error(err.response?.data || err));
  }
}
};
</script>