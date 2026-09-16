<template>
    <!-- Lietotāju profils, iziet -->
    <v-app-bar elevation="0" height="110" class="border-b">
        <img
            src="../../assets/img/logo.png"
            alt="Logo"
            class="logo"
            @click="$router.push('/')"
        />
        <v-spacer />
        <div class="d-flex align-center mr-4">
            <p class="text-small mr-4">
                {{ user?.name }} {{ user?.surname }}
            </p>
            <v-menu
                location="bottom end"
                transition="scale-transition"
            >
                <template v-slot:activator="{ props }">
                    <v-btn
                        v-bind="props"
                        icon
                        variant="text"
                    >
                        <v-icon size="30">
                            mdi-account-circle-outline
                        </v-icon>
                    </v-btn>
                </template>
                <v-list min-width="200">
                    <v-list-item
                        prepend-icon="mdi-account-outline"
                        title="Mans profils"
                        @click="goTo('/profile')"
                    />
                    <v-list-item
                        prepend-icon="mdi-logout"
                        title="Iziet"
                        @click="logout"
                    />
                </v-list>
            </v-menu>
        </div>
    </v-app-bar>
    <v-spacer />
      <v-navigation-drawer class="pt-10 d-flex flex-colum ">
        <v-list  class="mt-10">
        <v-list-item
            v-for="(link, index) in links"
            :key="link"
            link
            class="hover-effect ml-5 text-2xl"
            @click="goTo(linkRoutes[index])"
        >
            <v-list-item-title class="text-text text-h7 mb-6"><v-icon class="mr-5 ml-5">{{ icons[index] }}</v-icon> {{ link }}</v-list-item-title>
        </v-list-item>
        </v-list>
    <v-spacer class="mb-7"></v-spacer>
    <!-- Administratora saites -->
    <v-list>
        <v-list-item
            v-for="(link, index) in leaderLinks"
            :key="link"
            link
            class="hover-effect ml-5 text-2xl"
            @click="goTo(leaderRoutes[index])"
        >
            <v-list-item-title class="text-text text-h7 mb-6 text-wrap text-break"><v-icon class="mr-5 ml-5">{{ leaderIcons[index] }}</v-icon>{{ link }}</v-list-item-title>
        </v-list-item>
    </v-list>
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
      <v-btn @click="goTo('/danceGroup/controls')">
        <v-icon>
            mdi-account-group
        </v-icon>

        <span>Pārvaldība</span>
      </v-btn>
    </v-bottom-navigation>
</template>


<script>
import { useAuthStore } from '@/stores/auth'

export default {
    name: 'UserNavBar',

    props: {
        user: {
            type: Object,
            default: null,
        },
    },

    data() {
        return {
            drawer: false,
            authStore: useAuthStore(),
            linkRoutes: ['/posts', '/dance-groups', '/calender', '/admisson'],
            links: ['Raksti','Kolektīvi', 'Pasākumi', 'Uzņemšana'],
            icons:[ 'mdi-view-grid', 'mdi-account-group', 'mdi-calendar-month','mdi-hand-wave-outline'],
            leaderRoutes: ['/danceGroup/controls'],
            leaderIcons: ['mdi-account-group'],
            leaderLinks: ['Kolektīvu pārvaldība'],
        }
    },

    methods: {
        goTo(route) {
            this.$router.push(route)
            this.drawer = false
        },

        async logout() {
            await this.authStore.logout()

            this.$router.push('/')
        },
    },
}
</script>
