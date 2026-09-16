<template>
  <v-container fluid class="pa-0">
    <v-row justify="center" class="fill-height">
      <v-col
        v-if="group"
        cols="12"
        md="10"
        lg="9"
      >
        <v-card
          elevation="2"
          class="rounded-0 mt-8"
          min-height="600"
        >
          <v-row no-gutters class="h-100">
            <!-- KREISĀ PUSE -->
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column pa-8"
            >
              <!-- Atpakaļ poga -->
              <div class="d-flex justify-start">
                <v-btn
                  variant="text"
                  prepend-icon="mdi-arrow-left"
                  @click="$router.back()"
                >
                </v-btn>
              </div>
              <!-- Par kolektīvu -->
              <div class="d-flex align-center justify-space-between mt-4 text-small">
                <h2 class="text-small">
                  Par kolektīvu
                </h2>

                <v-chip
                class="border bg-accent text-white"
                >
                  {{ translatedGenre || 'Nav norādīts' }}
                </v-chip>
              </div>

              <v-divider class="my-7"></v-divider>

              <!-- Vadītāji -->
              <div class="mb-10">
                  <h3 class="text-small">
                    Vadītāji:
                  </h3>
                <p class="text-para">
                  {{
                    group?.leaders?.map(
                      l => `${l.user.name} ${l.user.surname}`
                    ).join(', ') || 'Nav vadītāju'
                  }}
                </p>
              </div>
              <!-- Atrašanās vieta -->
              <div class="mb-3 d-flex flex-row">
                <v-icon>
                  mdi-map-marker-outline
                </v-icon>
                <p class="text-para ml-3">
                  {{ group?.city }}, {{ group?.address }}
                </p>
              </div>
              <!-- Dalībnieki -->
              <div class="d-flex flex-row">
                <v-icon>
                  mdi-account-group-outline
                </v-icon>
                <p class="text-para ml-3">
                  Dalībnieki: {{ group?.dancers?.length || 0 }}
                </p>
              </div>

              <v-divider class="my-6"></v-divider>

              <!-- Apraksts -->
              <div class=" d-flex align-center">
                <p class="text-para text-center w-100">
                  {{ group?.description || 'Nav apraksta' }}
                </p>
              </div>
              
              <v-divider class="my-6"></v-divider>      

              <!-- Vecuma grupas -->
              <div class="mt-6">
                  <h3 class="text-small mb-4">
                    Vecuma grupas:
                  </h3>
                <div class="d-flex flex-wrap ga-2">
                  <v-chip class="bg-white"
                    v-for="ageGroup in group?.age_groups || []"
                    :key="ageGroup.id"
                    variant="outlined"
                    style="border-color: #02317A;"
                  >
                    {{ ageGroup.name }}: {{ ageGroup.age_group }}
                  </v-chip>

                  <v-chip
                    v-if="!(group?.age_groups?.length)"
                  >
                    Nav vecuma grupu
                  </v-chip>
                </div>
              </div>
            </v-col>
            <!-- LABĀ PUSE -->
            <v-col
              cols="12"
              md="6"
              class="d-flex flex-column pa-8 order-first order-md-last"
            >
              <!-- Pievienoties -->
              <div class="mt-6 text-right">
                <v-btn
                  v-if="canJoinGroup"
                  class="bg-accent text-small"
                  @click="joinGroup()"
                >
                  Pievienoties kolektīvam
                </v-btn>
              </div>
              <!-- Kolektīva nosaukums -->
              <h2
                class="text-accent text-accents mt-6 text-uppercase text-center"
              >
                {{ group?.name || 'KOLEKTĪVS' }}
              </h2>
              <!-- Bilde -->
              <div class="flex-grow-1 d-flex align-center justify-center py-6">
                <v-img
                  :src="
                    group?.picture_url ||
                    `../../../assets/img/hero.png`
                  "
                  width="100%"
                  max-width="600"
                  height="350"
                  contain
                  alt="Kolektīva attēls"
                />
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <!-- Datu ielādēšana -->
      <v-col
        v-else
        cols="12"
        class="text-center"
      >
        <v-progress-circular
          indeterminate
          color="primary"
          size="64"
        ></v-progress-circular>

        <p class="mt-4">
          Ielādē kolektīva datus...
        </p>
      </v-col>

    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";
import { useAuthStore } from '@/stores/auth'

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
      loading: true,
    };
  },
  computed: {
    authStore() {
      return useAuthStore();
    },
    translatedGenre() {
      return this.group?.genre ? (genreMap[this.group.genre] || this.group.genre) : 'Nav norādīts';
    },
    canJoinGroup() {
      if (
        !this.group ||
        this.authStore.loading ||
        !this.authStore.isAuthenticated
      ) {
        return false;
      }

      const alreadyMember = this.authStore.danceGroupMembers.some(
        member =>
          Number(member.dance_group?.id) === Number(this.group.id) &&
          (member.status === 'approved' || member.status === 'waiting') &&
          (member.role === 'dancer' || member.role === 'leader')
      );

      if (alreadyMember) {
        return false;
      }

      if (!this.ageGroups || this.ageGroups.length === 0) {
        return false;
      }

      return true;
    }
  },
  async mounted() {
    const authStore = useAuthStore()
    await Promise.all([
      authStore.fetchUser(),
      this.fetchGroupInfo()
    ]);

    this.loading = false;
  },
  methods: {
    async fetchGroupInfo() {
      const groupId = this.$route.params.id;
      if (!groupId) return;

      const res = await axios.get(`/api/dance-group-info/${groupId}`);
      this.group = res.data.data;

      console.log(res)

      if (this.group.age_groups) {
        this.ageGroups = this.group.age_groups;
      }
    },

    joinGroup() {
      const groupId = this.$route.params.id;

      if (!groupId || !this.authStore.user) return;

      axios.post('/api/members/join', {
        user_id: this.authStore.user.id,
        dance_group_id: groupId
      }, {
        withCredentials: true
      })
      .then(async () => {

        // Atjauno lietotāja kolektīvu datus
        await this.authStore.fetchUser();

        alert('Vadītājs saņems paziņojumu un apstiprinās Jūsu dalību.');
      })
      .catch(err => {
        alert(
          err.response?.data?.message ||
          'Neizdevās pievienoties.'
        );
      });
    },
  }
};
</script>
