<template>
  <v-app>
    <UserNavBar v-if="user" :user="user" />

    <NavBar v-else />

    <v-main>
      <RouterView />
    </v-main>

    <Footer />
  </v-app>
</template>

<script>
import axios from "axios";
import UserNavBar from "./content/UserNavBar.vue";
import NavBar from "./content/NavBar.vue";
import Footer from "./content/Footer.vue";

export default {
  components: { UserNavBar, NavBar, Footer },

  data() {
    return {
      user: null,
    };
  },

  async mounted() {
    try {
      const res = await axios.get("/api/profile", {
        withCredentials: true,
      });

      this.user = res.data.user ?? null;
    } catch (e) {
      this.user = null;
    }
  },
};
</script>

<style scoped>
h1 {
  color: #333;
}
</style>