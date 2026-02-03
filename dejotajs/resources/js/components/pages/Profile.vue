<template>
    <v-container>
        <h1 class="text-center mt-8">Lietotāja profils</h1>
        <v-card class="mx-auto my-8 pb-7" color="primary" elevation="16" max-width="600">
            <v-card-title :location="$vuetify.display.mobile ? 'text-h5' : 'text-h4'" >Lietotājs: {{ user.name }} {{ user.surname }}</v-card-title>
            <v-card-text>E-pasts: {{ user.email }}</v-card-text>
            <div v-for="group in user.dance_groups" :key="group.id">
                <v-card-text class="text-center"> Kolektīvs: {{ group.name }}</v-card-text>
                <v-card-subtitle v-if="group.role === 'dancer'"  class="text-center">Dejotājs, Vecuma grupa: {{ group.age_group }}</v-card-subtitle>
                <v-card-subtitle v-else-if="group.role === 'leader'" class="text-center">Vadītājs</v-card-subtitle>
            </div>
        </v-card>
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
  async mounted() {
    try {
      const res = await axios.get('api/profile', { withCredentials: true });

      if (res.data.data.length) {
        const profileData = res.data.data;

        this.user.name = profileData[0].app_user.name;
        this.user.surname = profileData[0].app_user.surname;
        this.user.email = profileData[0].app_user.email;

        this.user.dance_groups = profileData.map(member => ({
          id: member.dance_group.id,
          name: member.dance_group.name,
          role: member.role,
          age_group: member.age_group?.age_group || null
        }));
      }
    }  catch (err) {
      this.error = 'Neizdevās ielādēt profilu';
      console.error(err);

      if (err.response && err.response.status === 401) {
        this.$router.push('/login');
      }
    }
  }
}
</script>
