<template>
  <v-app-bar :elevation="2" class="bg-primary">
    <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    <v-app-bar-title class="app-bar-title text-center" @click="this.$router.push('/')" style="cursor: pointer;">
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

        <v-list>
          <v-list-item v-if="isLeader()" @click="$router.push('/danceForm')">
            <v-list-item-title>Izveidot kolektīvu</v-list-item-title>
          </v-list-item>
          <v-list-item v-for="group in userGroups" :key="group.id"  @click="$router.push(`/group/${group.dance_group.id}`)">
            <v-list-item-title>{{ group.dance_group.name || 'Nav kolektīvu' }}</v-list-item-title>
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
          <v-list-item @click="logout">
            <v-list-item-title>Iziet</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>

      <v-btn icon @click="toggleTheme" class="me-3">
        <v-icon>
          {{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}
        </v-icon>
      </v-btn>
    </div>
  </v-app-bar>

  <v-navigation-drawer v-model="drawer" :location="$vuetify.display.mobile ? 'bottom' : undefined" temporary>
    <v-list v-if="isAdmin">
      <v-list-item link @click="goTo(adminRoutes[index])" v-for="(link, index) in adminLinks" :key="link">
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>
    <v-list v-else>
      <v-list-item link @click="goTo(linkRoutes[index])" v-for="(link, index) in links" :key="link">
        <v-list-item-title>{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      menuMessage: false,  
      menuProfile: false,  
      drawer: false,
      isDark: false,
      isAdmin: false,
      userGroups: [],
      links: [
        'Sākums', 
        'Deju grupas', 
        'Kalendārs', 
        'Uzņemšana'
      ],
      linkRoutes: [
        '/', 
        '/', 
        '/', 
        '/'
      ],
      adminLinks: [
        'Admin Panelis',
        'Sākums', 
        'Deju grupas', 
        'Kalendārs', 
        'Uzņemšana'
      ],
      adminRoutes: [
        '/dashboard', 
        '/', 
        '/', 
        '/', 
        '/'
      ]
    };
  },
  methods: {
    toggleDrawer() {
      this.drawer = !this.drawer;
    },
    handleClick(link) {
      this.drawer = false;
    },
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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        this.$root.loggedIn = false;
        this.$root.user = null;
        this.$router.push('/login');
      } catch (e) {
        console.error('Logout failed', e);
      }
    },
    isLeader() {
      return this.userGroups.some(group => group.role === 'leader');
    }
  },
  async mounted() {
    try {
      const res = await axios.get('api/profile', { withCredentials: true });
      this.user.role = res.data.user.role;
      this.userGroups = res.data.dance_group_members || []; 

      this.isLeaderRole = this.isLeader();

      if (this.user.role === 'admin') {
        this.isAdmin = true;
      }
    } catch (err) {
      this.error = 'Neizdevās noteikt lomu';
      console.error(err);
    }
  }
};
</script>
