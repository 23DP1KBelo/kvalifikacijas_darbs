<template>
      <v-row>
        <!-- HERO daļa -->
        <v-col
          md="7"
          class="d-none d-md-flex align-center justify-center" style="height: 100vh;"
        >
          <div class="d-flex flex-column pl-10 ">
            <h2 class="text-accents text-accent">Esi daļa</h2>
            <div class="d-flex justify-center"> 
              <img src="../../assets/img/logo.png" alt="Logo" class="w-50 text-right" > 
            </div>
            <h2 class="text-accents text-accent text-right">no DEJAS!</h2>
          </div>
        </v-col>

        <!-- Pieslēgāsnās forma -->
        <v-col
          cols="12"
          md="5"
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
import axios from 'axios';

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
  try {

    // 1. CSRF cookie
    await axios.get('/sanctum/csrf-cookie');

    // 2. login
    await axios.post('/api/login', {
      email: this.email,
      password: this.password,
    });

    // 3. user
    const userResponse = await axios.get('/api/user');
    const user = userResponse.data;

    localStorage.setItem('user', JSON.stringify(user));

    this.$root.user = user;
    this.$root.loggedIn = true;

    this.$router.push(user.role === 'admin' ? '/dashboard' : '/');

  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      this.error = Object.values(err.response.data.errors)
            .flat()
            .map(e => `• ${e}`)
            .join('\n');
    } else if (err.response && err.response.data && err.response.data.message) {
        this.error = err.response.data.message;
    } else {
        this.error = 'Nezināma kļūda. Mēģiniet vēlreiz.';
    }
  }
}
  }
}

</script>