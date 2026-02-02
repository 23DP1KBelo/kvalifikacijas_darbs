<template>
    <v-app-bar :elevation="2" class="bg-primary" >
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-app-bar-title class="app-bar-title text-center">Dejotājs</v-app-bar-title>
        <v-btn icon @click="toggleTheme">
            <v-icon>
                {{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}
            </v-icon>
        </v-btn>
    </v-app-bar>

  <v-navigation-drawer      
    v-model="drawer"
    :location="$vuetify.display.mobile ? 'bottom' : undefined"
    temporary>
    <v-list>
      <v-list-item
        v-for="link in links"
        :key="link"
        link
        @click="handleClick(link)"
      >
        <v-list-item-title>{{ link }}</v-list-item-title>
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
      links: [
        'Sākums',
        'Deju grupas',
        'Kalendārs',
        'Uzņemšana',
      ],
    }
  },
  methods: {
    toggleDrawer() {
      this.drawer = !this.drawer
    },
    handleClick(link) {
      this.drawer = false
    },
    toggleTheme() {
      this.isDark = !this.isDark
      this.$vuetify.theme.global.name = this.isDark ? 'dark' : 'light'
    }
  }
}
</script>
