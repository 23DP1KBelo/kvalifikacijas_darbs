<template>
  <v-app>
    <UserNavBar v-if="loggedIn" :user="user"/>
    <NavBar v-else/>
    <v-main>
      <RouterView />
    </v-main>
    <Footer/>
  </v-app>
</template>

<script>
import axios from '../axion';
import Footer from './content/Footer.vue';
import NavBar from './content/NavBar.vue';
import UserNavBar from './content/UserNavBar.vue';

export default {
  name: 'App',
  components: {
    Footer,
    NavBar,
    UserNavBar,
  },
  data() {
    return {
      loggedIn: false,
      user: null
    };
  },
  async mounted() {
    try {
      const response = await axios.get('/user');
      this.loggedIn = response.data.logged_in;
      this.user = response.data.user;
    } catch {
      this.loggedIn = false;
      this.user = null;
    }
  }
};
</script>

<style scoped>
h1 {
  color: #333;
}
</style>