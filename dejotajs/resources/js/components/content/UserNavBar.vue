<template>
  <v-app-bar :elevation="2" class="bg-primary">
    <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    <v-app-bar-title class="app-bar-title text-center" @click="$router.push('/')" style="cursor: pointer;">
      Dejotājs
    </v-app-bar-title>

    <div class="d-flex align-center">
      <!-- Group menu -->
      <v-menu v-model="menuMessage" offset-y class="ms-3 p-0 m-0">
        <template #activator="{ props, attrs }">
          <v-btn icon v-bind="{ ...props, ...attrs }">
            <v-icon>mdi-message</v-icon>
          </v-btn>
        </template>

        <v-list v-if="userGroups.length > 0">
          <v-list-item v-for="group in userGroups" :key="group.id" @click="$router.push(`/group/${group.id}`)">
            <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
          </v-list-item>
        </v-list>

        <v-list v-else>
          <v-list-item>
            <v-list-item-title>Nav pievienotu kolektīvu</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <!-- Profile menu -->
      <v-menu v-model="menuProfile" offset-y class="ms-3 p-0 m-0">
        <template #activator="{ props, attrs }">
          <v-btn icon v-bind="{ ...props, ...attrs }">
            <v-icon>mdi-account-circle</v-icon>
          </v-btn>
        </template>

        <v-list>
          <v-list-item @click="$router.push('/profile')">
            <v-list-item-title>Profils</v-list-item-title>
          </v-list-item>
          <v-list-item @click="$router.push('/dance-leader')">
            <v-list-item-title>Kļūt par vadītāju</v-list-item-title>
          </v-list-item>
          <v-list-item @click="logout">
            <v-list-item-title>Iziet</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-btn icon @click="toggleTheme" class="me-3">
        <v-icon>{{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}</v-icon>
      </v-btn>
    </div>
  </v-app-bar>

  <v-navigation-drawer v-model="drawer" :location="$vuetify.display.mobile ? 'bottom' : undefined" temporary>
    <v-list>
      <v-list-item link v-for="(link, index) in links" :key="link" @click="goTo(linkRoutes[index])">
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>

    <!-- Admin section -->
    <v-list v-if="isAdmin">
      <v-list-item class="bg-secondary mt-8">
        <v-list-item-title>Administratora lapas</v-list-item-title>
      </v-list-item>
      <v-list-item link v-for="(link, index) in adminLinks" :key="link" @click="goTo(adminRoutes[index])">
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>
<!-- Leader section -->
<v-list v-if="leaderGroups.length > 0">
  <v-list-item class="bg-secondary mt-8">
    <v-list-item-title>Vadītāja lapas</v-list-item-title>
  </v-list-item>

  <v-list-item link @click="goTo('/danceForm')">
    <v-list-item-title>Kolektīva izveide</v-list-item-title>
  </v-list-item>

  <v-list-item @click="showGroups = !showGroups" class="cursor-pointer">
    <v-list-item-title>Vadītāju apstiprināšana</v-list-item-title>
  </v-list-item>
  <v-list v-if="showGroups">
    <v-list-item v-for="group in leaderGroups" :key="group.id" @click="$router.push(`/leaderApproval/${group.id}`)" class="ml-4 cursor-pointer">
      <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
    </v-list-item>
  </v-list>

  <v-list-item @click="showDancerGroups = !showDancerGroups" class="cursor-pointer">
    <v-list-item-title>Dejotāju apstiprināšana</v-list-item-title>
  </v-list-item>
  <v-list v-if="showDancerGroups">
    <v-list-item v-for="group in leaderGroups" :key="group.id" @click="$router.push(`/dancerApproval/${group.id}`)" class="ml-4 cursor-pointer">
      <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
    </v-list-item>
  </v-list>
</v-list>

<!-- Dancer section -->
<v-list v-if="dancerGroups.length > 0">
    <v-list-item class="bg-secondary mt-8">
    <v-list-item-title>Dejotāja kolektīvi</v-list-item-title>
  </v-list-item>
  <v-list-item v-for="group in dancerGroups" :key="group.id" @click="$router.push(`/group/${group.id}`)">
    <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
  </v-list-item>
</v-list>
  </v-navigation-drawer>
</template>

<script>
import axios from 'axios';

export default {
  props: { 
    user: { type: Object, required: true } // props paliek nemainīgs
  },
  data() {
    return {
      menuMessage: false,
      menuProfile: false,
      showGroups: false,
      showDancerGroups: false,
      drawer: false,
      isDark: false,
      isAdmin: false,
      userGroups: [],    // visi apstiprinātie kolektīvi
      leaderGroups: [],  // grupas, kur lietotājs ir leader
      dancerGroups: [],  // grupas, kur lietotājs ir dejotājs
      localUser: {},     // lokāla kopija props, ar kuru strādāsim
      links: ['Sākums', 'Kolektīvi', 'Kalendārs', 'Uzņemšana'],
      linkRoutes: ['/', '/dance-groups', '/', '/'],
      adminLinks: ['Admin Panelis'],
      adminRoutes: ['/dashboard']
    };
  },
  methods: {
    toggleDrawer() { this.drawer = !this.drawer; },
    toggleTheme() {
      this.isDark = !this.isDark;
      this.$vuetify.theme.global.name = this.isDark ? 'dark' : 'light';
    },
    goTo(route) { this.drawer = false; this.$router.push(route); },
    async logout() {
      try {
        await fetch('/logout', {
          method: 'POST',
          credentials: 'include',
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        });
        this.$root.loggedIn = false;
        this.$root.user = null;
        this.$router.push('/login');
      } catch (e) { console.error('Logout failed', e); }
    }
  },
  async mounted() {
    try {
      // Iegūstam lietotāja profila datus no API
      const res = await axios.get('api/profile', { withCredentials: true });
      this.localUser = res.data.user; // strādājam ar lokālo kopiju, nevis props

      // Ielādē visus apstiprinātos kolektīvus
      const groupRes = await axios.get('/api/groupListApproved', { withCredentials: true });
      const groups = groupRes.data.data || [];

      this.userGroups = groups;

      // Filtrē vadītāja grupas
      this.leaderGroups = groups.filter(g =>
        g.members.some(m => m.user.id === this.localUser.id && m.role === 'leader')
      );

      // Filtrē dejotāja grupas (kas nav leader)
      this.dancerGroups = groups.filter(g =>
        g.members.some(m => m.user.id === this.localUser.id && m.role !== 'leader')
      );

      // Nosaka, vai lietotājs ir admins
      if (this.localUser.role === 'admin') this.isAdmin = true;

      console.log('Local User:', this.localUser);
      console.log('Leader groups:', this.leaderGroups);
      console.log('Dancer groups:', this.dancerGroups);

    } catch (err) {
      console.error('Neizdevās ielādēt kolektīvus', err);
    }
  },
  computed: {
    isLeaderRole() {
      return this.leaderGroups.length > 0;
    }
  }
};
</script>