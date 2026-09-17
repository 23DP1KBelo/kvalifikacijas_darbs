<template>
      <v-row>
        <!-- HERO daļa -->
        <v-col
          md="5"
          class="d-none d-md-flex align-center justify-center" style="height: 100vh;"
        >
          <div class="d-flex flex-column pl-10 ">
            <h2 class="text-accents text-accent">Esi daļa</h2>
            <h2 class="text-accents text-accent text-right">no DEJAS!</h2>
          </div>
        </v-col>

        <!-- Reģistrācijas forma -->
       <v-col 
        cols="12" 
        md="7" 
        class="d-flex flex-column justify-center align-center h-100 login-column"
        >
            <v-spacer class="d-none d-md-flex" style="height: 50px;"></v-spacer>
            <h1 class="text-accents text-accent mb-10">
                REĢISTRĒJIES!
            </h1>
            <v-form class="w-75 fast-fail">
                <!-- Vārds,  Uzvārds -->
                <v-row>
                <v-col cols="12" md="6">
                    <v-text-field
                    label="Vārds"
                    :rules="nameRules"
                    v-model="name"
                    required
                />
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field
                    label="Uzvārds"
                    :rules="surnameRules"
                    v-model="surname"
                    required
                />
                </v-col>
                </v-row>
                <!-- E-pasts, Tālrunis -->
                <v-row>
                <v-col cols="12" md="6">
                    <v-text-field
                    label="E-pasts"
                    :rules="emailRules"
                    v-model="email"
                    required
                />
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field
                    label="Tālrunis"
                    :rules="phoneRules"
                    v-model="phone_number"
                    required
                />
                </v-col>
                </v-row>
                <!-- Parole -->
                <v-row>
                <v-col cols="12">
                    <v-text-field
                        label="Parole"
                        :rules="passwordRules"
                        v-model="password"
                        type="password"
                        required
                    />
                </v-col>
                </v-row>
                <!-- Paroles apstiprinājums -->
                <v-row>
                <v-col cols="12">
                    <v-text-field
                        label="Paroles apstiprinājums"
                        v-model="password_confirmation"
                        :rules="passwordConfirmationRules"
                        type="password"
                        required
                    />
                </v-col>
                </v-row>
                <v-alert
                    v-if="error"
                    type="error"
                    dense
                    outlined
                    class="mt-3"
                    style="white-space: pre-line"
                    >
                    {{ error }}
                </v-alert>
                <div class="d-flex justify-center align-center text-center mb-4">
                    <v-btn 
                        class="mt-2 mb-4 bg-accent text-login text-none"
                        @click="registration"
                    >
                        Reģistrēties
                    </v-btn>
                </div>
            </v-form>
        </v-col>
      </v-row>
</template>

<style scoped>
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
    data(){
        return {
            name: '',
            surname: '',
            role: '',
            email: '',
            password: '',
            password_confirmation: '',
            phone_number: '',
            error: '',
        emailRules: [
        v => !!v || 'E-pasts ir obligāts',
        v => /.+@.+\..+/.test(v) || 'Lūdzu, ievadiet derīgu e-pasta adresi'
      ],
        surnameRules: [
        v => !!v || 'Uzvārds ir obligāts'
      ],
      nameRules: [
        v => !!v || 'Vārds ir obligāts'
      ],
      phoneRules: [
        v => !!v || 'Tālrunis ir obligāts',
        v => /^\+?\d{7,15}$/.test(v) || 'Lūdzu, ievadiet derīgu tālruņa numuru'
      ],
      passwordRules: [
        v => !!v || 'Parole ir obligāta',
        v => v.length >= 8 || 'Parolei jābūt vismaz 8 rakstzīmēm'
      ],
      passwordConfirmationRules: [
        v => !!v || 'Paroles apstiprinājums ir obligāts'
      ]
        }
    },
    methods: {
        async registration(){
            try {
                if(this.password !== this.password_confirmation){
                    this.error = 'Paroles nesakrīt.';
                    return;
                }
                const response = await axios.post('/api/register', {
                    name: this.name,
                    surname: this.surname,
                    email: this.email,
                    password: this.password,
                    phone_number: this.phone_number,
                });
                this.$router.push('/login');
                alert('Reģistrācija veiksmīga! Tagad varat pieslēgties.');
            }  catch (err) {
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
