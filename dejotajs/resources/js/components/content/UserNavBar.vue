<template>
  <v-app-bar elevation="2" class="bg-primary text-white d-flex justify-space-between">
    <v-app-bar-nav-icon @click.stop="drawer = !drawer" class="text-white" aria-label="Navigācija"></v-app-bar-nav-icon>
    <v-app-bar-title
      class="app-bar-title text-h4 text-center clickable"
      @click="$router.push('/')"
    >
      Dejotājs
    </v-app-bar-title>
    <div class="d-flex align-center">
      <v-menu v-model="menuMessage" offset-y class="ms-3">
        <template #activator="{ props, attrs }">
          <v-btn icon v-bind="{ ...props, ...attrs }" class="text-white" aria-label="kolektīvi">
            <v-icon>mdi-message</v-icon>
          </v-btn>
        </template>
        <v-list class="bg-surface rounded-lg">
          <v-list-item
            v-for="group in userGroups"
            :key="group.id"
            @click="$router.push(`/group/${group.id}`)"
            class="hover-bg-softblue rounded-lg my-1"
          >
            <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
          </v-list-item>
          <v-list-item v-if="userGroups.length === 0" class="rounded-lg my-1">
            <v-list-item-title>Nav pievienotu kolektīvu</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
      <v-menu v-model="menuProfile" offset-y class="ms-3">
        <template #activator="{ props, attrs }">
          <v-btn icon v-bind="{ ...props, ...attrs }" class="text-white" aria-label="Profils">
            <v-icon>mdi-account-circle</v-icon>
          </v-btn>
        </template>

        <v-list class="bg-surface rounded-lg">
          <v-list-item @click="$router.push('/profile')" class="hover-bg-softblue rounded-lg my-1">
            <v-list-item-title>Profils</v-list-item-title>
          </v-list-item>
          <v-list-item @click="$router.push('/dance-leader')" class="hover-bg-softblue rounded-lg my-1">
            <v-list-item-title>Kļūt par vadītāju</v-list-item-title>
          </v-list-item>
          <v-list-item @click="logout" class="hover-bg-softblue rounded-lg my-1">
            <v-list-item-title>Iziet</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <!-- Theme toggle -->
      <v-btn icon class="ms-3 text-white" @click="toggleTheme" aria-label="Fona maiņa">
        <v-icon>{{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}</v-icon>
      </v-btn>
    </div>
  </v-app-bar>

  <!-- Navigation Drawer -->
  <v-navigation-drawer
    v-model="drawer"
    :location="$vuetify.display.mobile ? 'bottom' : undefined"
    temporary
    class="bg-surface"
  >
    <!-- Main Links -->
    <v-list>
      <v-list-item
        link
        v-for="(link, index) in links"
        :key="link"
        @click="goTo(linkRoutes[index])"
        class="hover-bg-softblue rounded-lg my-1"
      >
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>

    <!-- Admin Links -->
    <v-list v-if="isAdmin" class="mt-6">
      <v-list-item class="bg-secondary rounded-lg px-2 py-1">
        <v-list-item-title>Administratora lapas</v-list-item-title>
      </v-list-item>
      <v-list-item
        link
        v-for="(link, index) in adminLinks"
        :key="link"
        @click="goTo(adminRoutes[index])"
        class="hover-bg-softblue rounded-lg my-1"
      >
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>

    <!-- Leader Links -->
    <v-list v-if="leaderGroups.length > 0" class="mt-6">
      <v-list-item class="bg-secondary rounded-lg px-2 py-1">
        <v-list-item-title>Vadītāja lapas</v-list-item-title>
      </v-list-item>

      <v-list-item link @click="goTo('/danceForm')" class="hover-bg-softblue rounded-lg my-1">
        <v-list-item-title>Kolektīva izveide</v-list-item-title>
      </v-list-item>

      <v-list-item @click="showGroups = !showGroups" class="cursor-pointer hover-bg-softblue">
        <v-list-item-title>Vadītāju apstiprināšana</v-list-item-title>
      </v-list-item>
      <v-list v-if="showGroups">
        <v-list-item
          v-for="group in leaderGroups"
          :key="group.id"
          @click="$router.push(`/leaderApproval/${group.id}`)"
          class="ml-4 hover-bg-softblue rounded-lg my-1"
        >
          <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
        </v-list-item>
      </v-list>

      <v-list-item @click="showDancerGroups = !showDancerGroups" class="cursor-pointer hover-bg-softblue rounded-lg my-1">
        <v-list-item-title>Dejotāju apstiprināšana</v-list-item-title>
      </v-list-item>
      <v-list v-if="showDancerGroups">
        <v-list-item
          v-for="group in leaderGroups"
          :key="group.id"
          @click="$router.push(`/dancerApproval/${group.id}`)"
          class="ml-4 hover-bg-softblue rounded-lg my-1"
        >
          <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-list>

    <!-- Dancer Links -->
    <v-list v-if="dancerGroups.length > 0" class="mt-6">
      <v-list-item class="bg-secondary rounded-lg px-2 py-1">
        <v-list-item-title>Dejotāja kolektīvi</v-list-item-title>
      </v-list-item>
      <v-list-item
        v-for="group in dancerGroups"
        :key="group.id"
        @click="$router.push(`/group/${group.id}`)"
        class="hover-bg-softblue rounded-lg my-1"
      >
        <v-list-item-title>{{ group.name || 'Nav kolektīvu' }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    user: { type: Object, required: true },
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
      userGroups: [],
      leaderGroups: [],
      dancerGroups: [],
      localUser: {},
      links: ['Sākums','Aktualitātes', 'Kolektīvi', 'Kalendārs', 'Uzņemšana'],
      linkRoutes: ['/', '/posts','/dance-groups', '/calender', '/admisson'],
      adminLinks: ['Admin Panelis', 'Statistika'],
      adminRoutes: ['/dashboard', '/dashboard/stats'],
    };
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark;
      this.$vuetify.theme.global.name = this.isDark ? 'dark' : 'light';
    },
    goTo(route) {
      this.drawer = false;
      this.$router.push(route);
    },
    async logout() {
      try {
        await fetch('/logout', {
          method: 'POST',
          credentials: 'include',
          headers: {
            'X-CSRF-TOKEN': document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute('content'),
          },
        });
        this.$root.loggedIn = false;
        this.$root.user = null;
        this.$router.push('/login');
      } catch (e) {
        console.error('Logout failed', e);
      }
    },
  },
  async mounted() {
    try {
      const res = await axios.get('api/profile', { withCredentials: true });
      this.localUser = res.data.user;

      const groupRes = await axios.get('/api/groupListApproved', { withCredentials: true });
      const groups = groupRes.data.data || [];

      this.userGroups = groups;
      this.leaderGroups = groups.filter((g) =>
        g.members.some((m) => m.user.id === this.localUser.id && m.role === 'leader')
      );
      this.dancerGroups = groups.filter((g) =>
        g.members.some((m) => m.user.id === this.localUser.id && m.role !== 'leader')
      );

      if (this.localUser.role === 'admin') this.isAdmin = true;
    } catch (err) {
      console.error('Neizdevās ielādēt kolektīvus', err);
    }
  },
};
</script>

<style>
.clickable {
  cursor: pointer;
}
.hover-bg-softblue:hover {
  background-color: #B3CFE5 !important; /* tavu theme softblue */
}
.rounded-lg {
  border-radius: 12px;
}
</style>