<template>
     <v-container class="d-flex justify-center align-center mt-8">
        <v-card class=" bg-secondary" elevation="16" width="500px">
            <v-card-title class="text-center mb-4 mt-4">Reģistrēšanās</v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field label="Vārds" v-model="name" required></v-text-field>
                    <v-text-field label="Uzvārds" v-model="surname" required></v-text-field>
                    <v-text-field label="E-pasts" v-model="email" required></v-text-field>
                    <v-text-field label="Parole" v-model="password" type="password" required></v-text-field>
                    <v-text-field label="Paroles apstiprinājums" v-model="password_confirmation" type="password" required></v-text-field>
                    <v-text-field label="Tālrunis" v-model="phone_number" required></v-text-field>
                    <v-alert v-if="error" type="error" dense outlined class="mt-3">
                        {{ error }}
                    </v-alert>
                    <div class="d-flex justify-center align-center text-center mb-4">
                        <v-btn class="mt-4" color="primary" @click="registration">
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
    data(){
        return {
            name: '',
            surname: '',
            email: '',
            password: '',
            password_confirmation: '',
            phone_number: '',
            error: '',
        }
    },
    methods: {
        async registration(){
            try {
                if(this.password !== this.password_confirmation){
                    this.error = 'Paroles nesakrīt.';
                    return;
                }
                const response = await axios.post('/register', {
                    name: this.name,
                    surname: this.surname,
                    email: this.email,
                    password: this.password,
                    phone_number: this.phone_number,
                });
                console.log(response.data);
            }  catch (err) {
                if (err.response && err.response.data && err.response.data.errors) {
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
