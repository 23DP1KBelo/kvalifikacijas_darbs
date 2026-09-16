<template>
      <v-row>
        <!-- HERO daļa -->
        <v-col
          md="5"
          class="d-none d-md-flex align-center justify-center" style="height: 100vh;"
        >
          <div class="d-flex flex-column pl-10 ">
            <h2 class="text-accents text-accent">Esi daļa</h2>
            <h2 class="text-accents text-accent">no DEJAS!</h2>
          </div>
        </v-col>

        <!-- Pieslēgāsnās forma -->
        <v-col
          cols="12"
          md="7"
          class="d-flex flex-column justify-center align-center h-100 login-column"
        >
          <div class="w-100 d-none d-md-flex" style="height: 150px;"></div>
          <h1 class=" text-accents text-accent mb-10">PIESLĒDZIES!</h1>
          <v-form ref="form" v-model="valid" class="mt-4 w-75 mx-auto fast-fail">
            <v-text-field label="E-pasts" v-model="email" :rules="emailRules" required></v-text-field>
            <v-text-field label="Parole" v-model="password" type="password" :rules="passwordRules" required></v-text-field>
            <v-alert v-if="error" type="error" dense outlined class="mt-3" style="white-space: pre-line">
              {{ error }}
            </v-alert>
            <div class="d-flex justify-center align-center text-center flex-column">
              <v-btn class="mt-2 mb-4 bg-accent text-login text-none" @click="login">
                Pieslēgties
              </v-btn>
            </div>
          </v-form>
        </v-col>  
      </v-row>
</template>

<style>
.text-login{
  font-family: "Playfair Display", serif;
  font-optical-sizing: auto;
  font-size: 20px;
  letter-spacing: 0;
}

</style>

<script>
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

export default {
    data() {
        return {
            email: '',
            password: '',
            valid: false,
            error: '',

            emailRules: [
                v => !!v || 'E-pasts ir obligāts',
                v => /.+@.+\..+/.test(v) || 'Lūdzu, ievadiet derīgu e-pasta adresi'
            ],

            passwordRules: [
                v => !!v || 'Parole ir obligāta',
                v => v.length >= 6 || 'Parolei jābūt vismaz 6 rakstzīmēm'
            ]
        }
    },

    methods: {
        async login() {
            this.error = ''

            try {
                // 1. CSRF cookie
                await axios.get('/sanctum/csrf-cookie', {
                    withCredentials: true
                })

                // 2. Login
                await axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                }, {
                    withCredentials: true
                })

                // 3. Ielādē lietotāju Pinia store
                const auth = useAuthStore()

                await auth.fetchUser()

                // 4. Pāreja uz lapu
                this.$router.push(
                    auth.isAdmin ? '/dashboard' : '/'
                )

            } catch (err) {
                if (
                    err.response &&
                    err.response.data &&
                    err.response.data.errors
                ) {
                    this.error = Object.values(err.response.data.errors)
                        .flat()
                        .map(e => `• ${e}`)
                        .join('\n')

                } else if (
                    err.response &&
                    err.response.data &&
                    err.response.data.message
                ) {
                    this.error = err.response.data.message

                } else {
                    this.error = 'Nezināma kļūda. Mēģiniet vēlreiz.'
                }
            }
        }
    }
}
</script>