<template>
  <v-app-bar
    elevation="2"
    class="bg-primary text-white d-flex justify-space-between"
  >
    <v-app-bar-nav-icon @click.stop="drawer = !drawer" class="text-white"></v-app-bar-nav-icon>

    <!-- Title -->
    <v-app-bar-title
      class="app-bar-title text-h4 text-center  clickable "
      @click="$router.push('/')"
    >
      Dejotājs
    </v-app-bar-title>

    <!-- Right icons -->
    <div class="d-flex align-center">
      <!-- Login -->
      <v-btn icon class="text-white" @click="$router.push('/login')">
        <v-icon>mdi-account</v-icon>
      </v-btn>

      <!-- Theme toggle -->
      <v-btn icon class="text-white" @click="toggleTheme">
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
    <v-list>
      <v-list-item
        v-for="(link, index) in links"
        :key="link"
        link
        @click="goTo(linkRoutes[index])"
        class="hover-bg-softblue rounded-lg my-1"
      >
        <v-list-item-title class="text-text">{{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
export default {
  data() {
    return {
      drawer: false,
      isDark: false,
      links: ['Sākums', 'Aktualitātes','Kolektīvi', 'Kalendārs', 'Uzņemšana'],
      linkRoutes: ['/', '/posts', '/dance-groups', '/', '/admisson'],
    }
  },
  methods: {
    toggleTheme() {
      this.isDark = !this.isDark
      this.$vuetify.theme.global.name = this.isDark ? 'dark' : 'light'
    },
    goTo(route) {
      this.drawer = false
      this.$router.push(route)
    },
  },
}
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
