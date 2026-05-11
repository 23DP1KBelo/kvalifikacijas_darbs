<template>
  <v-container class="d-flex justify-center align-center mt-8">
        <v-card class=" bg-softblue" elevation="16" width="450px">
          <v-card-title class="text-h5 text-center py-4">Pieslēgšanās</v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid" class="mt-4">
              <v-text-field label="E-pasts" v-model="email" required></v-text-field>
              <v-text-field label="Parole" v-model="password" type="password" required></v-text-field>
              <v-alert v-if="error" type="error" dense outlined class="mt-3" style="white-space: pre-line">
                  {{ error }}
              </v-alert>
              <div class="d-flex justify-center align-center text-center flex-column">
                <v-btn class="mt-2 mb-4" color="primary" @click="login">
                  Pieslēgties
                </v-btn>
                <v-card-text class="mt-4">Neesi reģistrējies?</v-card-text>
                <v-btn class="mb-4" color="secondary" @click="$router.push('/register')">
                  Reģistrēties
                </v-btn>
              </div>
            </v-form>
          </v-card-text>
        </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      valid: false,
      error: '',
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