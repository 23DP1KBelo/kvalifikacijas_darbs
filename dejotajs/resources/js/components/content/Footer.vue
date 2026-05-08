<template>
    <v-footer class="d-flex align-center justify-center flex-wrap" color="primary" :style="{overflowY: 'auto'}" height="100px">
      <div v-if="!$vuetify.display.mobile" >
        <v-btn variant="text" @click="goTo(linkRoutes[index])" v-for="(link, index) in links" :key="link" rounded>{{ link }}</v-btn>
      </div>
      <div class="flex-1-0-100 text-center d-flex justify-center ga-sm-4">
        {{ new Date().getFullYear() }} <strong>Dejotajs</strong>
      </div>
    </v-footer>
</template>

<script>

export default {
   data() {
    return {
      links: [
        'Sākums', 
        'Kolektīvi',
        'Aktualitātes', 
        'Kalendārs', 
        'Uzņemšana',
      ],
      linkRoutes: [
        '/', 
        '/dance-groups', 
        '/posts',
        '/calender', 
        '/admisson'
      ],
    }
  },
  methods: {
    goTo(route) {
      this.drawer = false;
      this.$router.push(route);
    },
  }
}

</script>
