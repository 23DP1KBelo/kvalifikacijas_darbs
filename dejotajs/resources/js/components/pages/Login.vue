<template>
  <v-container class="d-flex justify-center align-center mt-8">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="4">
        <v-card>
          <v-card-title class="text-h5 text-center py-4">Pieslēgšanās</v-card-title>
          <v-card-text>
            <v-form ref="form" v-model="valid">
              <v-text-field label="E-pasts" v-model="email" required></v-text-field>
              <v-text-field label="Parole" v-model="password" type="password" required></v-text-field>

              <v-alert
                v-if="error"
                type="error"
                dense
                outlined
                class="mt-2"
              >
                {{ error }}
              </v-alert>
              <v-btn
                class="mt-4"
                color="primary"
                @click="login"
              >
                Pieslēgties
              </v-btn>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
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
        // Sanctum CSRF cookie
        await axios.get('/sanctum/csrf-cookie');

        const response = await axios.post('/login', {
          email: this.email,
          password: this.password,
        });

        console.log(response.data);

        // redirect pēc login
        this.$router.push('/');

      } catch (err) {
          if (err.response && err.response.data && err.response.data.errors) {
            // Laravel validācijas kļūdas
            this.error = Object.values(err.response.data.errors)
                              .flat()
                              .join(' ');
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