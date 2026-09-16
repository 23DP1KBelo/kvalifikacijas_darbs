<template>
  <v-app>

    <!-- Admin -->
    <AdminNavBar
      v-if="auth.isAdmin"
      :user="auth.user"
    />

    <!-- Leader -->
    <LeaderNavBar
      v-else-if="auth.isLeader"
      :user="auth.user"
    />

    <!-- Parasts lietotājs -->
    <UserNavBar
      v-else-if="auth.isAuthenticated"
      :user="auth.user"
    />

    <!-- Viesis -->
    <NavBar
      v-else
    />

    <v-main>
      <RouterView />
    </v-main>

    <Footer />

  </v-app>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

import NavBar from './content/NavBar.vue'
import UserNavBar from './content/UserNavBar.vue'
import LeaderNavBar from './content/LeaderNavBar.vue'
import AdminNavBar from './content/AdminNavBar.vue'
import Footer from './content/Footer.vue'

const auth = useAuthStore()

onMounted(() => {
  auth.fetchUser()
})
</script>