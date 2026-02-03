<template>
    <v-footer app class="d-flex align-center justify-center flex-wrap " color="primary" :style="{overflowY: 'auto'}" height="100px">
      <div v-if="!$vuetify.display.mobile" >
        <v-btn variant="text" v-for="link in links" :key="link" text rounded>{{ link }}</v-btn>
      </div>
      <div class="flex-1-0-100 text-center d-flex justify-center ga-sm-4">
        {{ new Date().getFullYear() }} <strong>Dejotajs</strong>
      </div>
    </v-footer>
</template>

<script>

export default {
  name: 'Footer',
  setup() {
    const links = [
      'Sākums',
      'Deju grupas',
      'Kalendārs',
      'Uzņemšana',
    ]
    return {
      links,
    }
  }
}

</script>
