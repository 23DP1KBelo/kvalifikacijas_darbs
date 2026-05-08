<template>
  <v-app-bar
    elevation="2"
    class="bg-primary text-white d-flex justify-space-between"
  >
    <v-app-bar-nav-icon @click.stop="drawer = !drawer" class="text-surface" aria-label="Navigācija"></v-app-bar-nav-icon>
    <v-app-bar-title
      class="app-bar-title text-h4 text-center clickable "
      @click="$router.push('/')"
    >
      Dejotājs
    </v-app-bar-title>
    <div class="d-flex align-center">
      <v-btn icon class="text-surface" @click="$router.push('/login')" aria-label="Pieslēgšanās">
        <v-icon aria-label="Pieslēgties">mdi-account</v-icon>
      </v-btn>
      <v-btn icon class="text-surface" @click="toggleTheme" aria-label="Fona maiņa">
        <v-icon>{{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}</v-icon>
      </v-btn>
    </div>
  </v-app-bar>
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
        class="hover-bg-secondary rounded-lg my-1"
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
      linkRoutes: ['/', '/posts', '/dance-groups', '/calender', '/admisson'],
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
  background-color: #B3CFE5 !important;
}
.rounded-lg {
  border-radius: 12px;
}
</style>
