<template>
<v-app-bar elevation="0" height="110">
  <img src="../../assets/img/logo.png" alt="Logo" class="logo" @click="$router.push('/')"/>

  <v-spacer/>

  <div class="d-flex align-center">
    <v-btn class="pt-0 pb-0 pl-0 text-black text text-none letter-spacing-0" variant="plain" @click="$router.push('/register')">Reģistrēties / </v-btn>
    <v-btn rounded="lg" class="bg-accent pt-0 pb-0 pl-4 pr-5 text text-none letter-spacing-0 mr-3" @click="$router.push('/login')">Pieslēgties</v-btn>
  </div>
</v-app-bar>
  <v-navigation-drawer class="pt-10 d-flex flex-colum border-0">
    <v-list  class="mt-10">
      <v-list-item
        v-for="(link, index) in links"
        :key="link"
        link
        @click="goTo(linkRoutes[index])"
      >
        <v-list-item-title class="text-text text-h7 mb-6"><v-icon class="mr-5 ml-5">{{ icons[index] }}</v-icon> {{ link }}</v-list-item-title>
      </v-list-item>
    </v-list>
    <!-- <v-btn icon @click="toggleTheme" aria-label="Fona maiņa">
        <v-icon>
          {{ isDark ? 'mdi-white-balance-sunny' : 'mdi-weather-night' }}
        </v-icon>
      </v-btn> -->
  </v-navigation-drawer>

   <v-bottom-navigation
      class="d-md-none"
      grow
      elevation="3"
    >
      <v-btn
        v-for="(link, index) in links"
        :key="link"
        @click="goTo(linkRoutes[index])"
      >
        <v-icon>
          {{ icons[index] }}
        </v-icon>

        <span>{{ link }}</span>
      </v-btn>
    </v-bottom-navigation>
</template>

<script>

export default {
  data() {
    return {
      isDark: false,
      links: ['Raksti','Kolektīvi', 'Pasākumi', 'Uzņemšana'],
      icons:[ 'mdi-view-grid', 'mdi-account-group', 'mdi-calendar-month','mdi-hand-wave-outline'],
      linkRoutes: ['/posts', '/dance-groups', '/calender', '/admisson'],
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
.logo {
  width: 178px;
  height: 100px;
  object-fit: contain;
  margin-top: 10px;
}
.text{
  font-family: "Playfair Display", serif;
  font-optical-sizing: auto;
  font-size: 16px;
  letter-spacing: 0;
}
</style>

