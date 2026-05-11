<template>
  <v-container fluid class="pa-0">
    <v-row justify="center" align="center" class="fill-height">
      <v-col cols="10" md="8" lg="6" v-if="group">
        <v-card elevation="2" class="mt-8">

          <v-img
            :src="group.picture_url || `https://picsum.photos/id/${group.id}/600/350`"
            height="500"
            class="card-full-image"
            alt="img_kolektīvs"
            cover>
          </v-img>

          <div v-if="canJoinGroup" class="d-flex justify-end mx-4">
            <v-btn class="mt-4 bg-secondary" @click="joinGroup()">
              Pievienoties kolektīvam
            </v-btn>
          </div>

          <div v-if="!canJoinGroup" class="d-flex justify-end mx-4 mt-4">
            <p>Kolektīvam nav iespējams pievienoties</p>
          </div>


          <v-divider class="my-4"></v-divider>

          <v-card-subtitle class="mt-8">{{ group?.city }}, {{ group?.address }}</v-card-subtitle>

          <v-card-text>
            <v-divider class="my-4"></v-divider>
            <p><strong>Dalībnieku skaits:</strong> {{ group?.dancers?.length || 0 }}</p>
            <p>
              <strong>Vadītāji:</strong>
              {{ group?.leaders?.map(l => `${l.user.name} ${l.user.surname}`).join(', ') || 'Nav vadītāju' }}
            </p>
            <v-divider class="my-4"></v-divider>
            
            <p><strong>Žanrs:</strong> {{ translatedGenre || 'Nav norādīts' }}</p>
            <p><strong>Apraksts:</strong> {{ group?.description || 'Nav apraksta' }}</p>

            <v-divider class="my-4"></v-divider>

            <p><strong>Vecuma grupas:</strong></p>
            <ul>
              <li v-for="ageGroup in group?.age_groups || []" :key="ageGroup.id">
                {{ ageGroup.name }} ({{ ageGroup.age_group || 'Nav informācijas' }})
              </li>
              <li v-if="!(group?.age_groups?.length)">Nav vecuma grupu</li>
            </ul>

            <v-divider class="my-4"></v-divider>
          </v-card-text>

          <v-card-actions>
            <v-btn class="bg-primary my-2 mx-2" @click="$router.back()">Atpakaļ</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Loading / fallback -->
      <v-col cols="12" class="text-center" v-else>
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
        <p>Ielādē kolektīva datus...</p>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";

const genreMap = {
  'lyrical dance': 'Liriskā deja',
  'contemporary dance': 'Mūsdienīgās dejas',
  'ballet': 'Balets',
  'hip hop': 'Hip-hops',
  'folk dance': 'Tautas dejas',
  'other': 'Cits'
};

export default {
  data() {
    return {
      group: null,
      ageGroups: [],
      user: null,
      loading: true,
    };
  },
  computed: {
    translatedGenre() {
      return this.group?.genre ? (genreMap[this.group.genre] || this.group.genre) : 'Nav norādīts';
    },
    canJoinGroup() {
      if (!this.group || !this.user) return false;
      if (!this.ageGroups || this.ageGroups.length === 0) return false;

      const userId = this.user.id;

      return !this.group.leaders?.some(leader => leader.user.id === userId);
    },
  },
  async mounted() {
    await Promise.all([
      this.fetchUser(),
      this.fetchGroupInfo()
    ]);

    this.loading = false;
  },
  methods: {
    async fetchUser() {
      try {
        const res = await axios.get('/api/profile', {
          withCredentials: true,
        });

        this.user = res.data.user;
      } catch (e) {
        this.user = null;
      }
    },

    async fetchGroupInfo() {
      const groupId = this.$route.params.id;
      if (!groupId) return;

      const res = await axios.get(`/api/dance-group-info/${groupId}`);
      this.group = res.data.data;

      if (this.group.age_groups) {
        this.ageGroups = this.group.age_groups;
      }
    },

    joinGroup() {
      const groupId = this.$route.params.id;
      if (!groupId) return;

      axios.post('/api/members/join', {
        user_id: this.user.id,
        dance_group_id: groupId
      }, {
        withCredentials: true
      })
      .then(() => {
        alert('Vadītājs saņems paziņojumu un apstiprinās Jūsu dalību.');
      })
      .catch(err => {
        alert(err.response?.data?.message || 'Neizdevās pievienoties.');
      });
    }
  }
};
</script>

<style scoped>
.fill-height {
  min-height: 100vh;
}
.v-card-title.headline {
  font-size: 2rem;
  font-weight: bold;
}
.v-card-text p {
  margin-bottom: 0.5rem;
}
ul {
  padding-left: 1.2rem;
}

.card-full-image {
  width: 100%;
  object-fit: cover;
}

</style>