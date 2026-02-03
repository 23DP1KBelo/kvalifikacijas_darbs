<template>
  <v-app>
    <NavBar/>
    <v-main>
      <RouterView />
    </v-main>
    <Footer/>
  </v-app>
</template>

<script>
import Footer from './content/Footer.vue';
import NavBar from './content/NavBar.vue';


export default {
  name: 'App',
  components: {
    Footer,
    NavBar
  },
};
</script>

<style scoped>
h1 {
  color: #333;
}
</style>