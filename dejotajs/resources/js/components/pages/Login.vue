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
              <v-btn class="mt-4 text-center d-flex justify-center" color="primary">
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
export default {
    data() {
        return {
        email: '',
        password: '',
        valid: false,
        }
    },
    methods: {
    },
}
</script>