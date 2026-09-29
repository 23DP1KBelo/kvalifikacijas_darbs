<template>
    <v-dialog
        v-model="dialog"
        max-width="700"
    >
        <v-card>
            <div>
                <div class="d-flex justify-start mt-4 ml-4">
                    <v-btn
                    variant="text"
                    @click="dialog = false"
                    >
                    <v-icon>mdi-arrow-left</v-icon>
                    </v-btn>
                </div>
                <h2 class="text-accent text-center ma-4">
                    IESTATĪJUMI
                </h2>
            </div>
            <v-divider />
            <v-row no-gutters>
                <!-- Kreisā izvēlne -->
                <v-col
                    cols="12"
                    md="4"
                >
                    <v-list>
                        <v-list-item
                            :active="activeSection === 'profile'"
                            @click="activeSection = 'profile'"
                        >
                            <template #prepend>
                                <v-icon>mdi-cog</v-icon>
                            </template>

                            <v-list-item-title class="text-para">
                                Profils
                            </v-list-item-title>
                        </v-list-item>

                        <v-list-item
                            :active="activeSection === 'security'"
                            @click="activeSection = 'security'"
                        >
                            <template #prepend>
                                <v-icon>mdi-lock-outline</v-icon>
                            </template>

                            <v-list-item-title class="text-para">
                                Drošība
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-col>
                <v-divider vertical />
                <!-- Labā puse -->
                <v-col
                    cols="12"
                    md="8"
                    class="pa-5"
                >
                    <!-- PROFILS -->
                    <div v-if="activeSection === 'profile'">

                        <h4 class="text-accent mb-5  text-center">
                            Profils
                        </h4>
                        <div class="d-flex flex-row ga-3">
                            <v-text-field
                                v-model="profileData.name"
                                label="Vārds"
                            />
                            <v-text-field
                                v-model="profileData.surname"
                                label="Uzvārds"
                            />
                        </div>
                        <v-text-field
                            v-model="profileData.email"
                            label="E-pasts"
                        />
                        <v-text-field
                            v-model="profileData.phone_number"
                            label="Tālrunis"
                        />
                        <v-alert
                            v-if="profileAlert.show"
                            :type="profileAlert.type"
                            @click:close="profileAlertlert.show = false"
                        >
                            {{ profileAlert.message }}
                        </v-alert>
                        <div class="d-flex justify-end mt-4">
                            <v-btn
                                color="accent"
                                @click="saveProfile"
                                class="text-small letter-spacing-0"
                            >
                                Saglabāt
                            </v-btn>
                        </div>
                    </div>
                    <!-- DROŠĪBA -->
                    <div v-else-if="activeSection === 'security'">
                        <h4 class="text-accent mb-5  text-center">
                            Paroles maiņa
                        </h4>
                        <v-text-field
                            v-model="passwordData.current_password"
                            label="Pašreizējā parole"
                            type="password"
                        />
                        <v-text-field
                            v-model="passwordData.password"
                            label="Jaunā parole"
                            type="password"
                        />
                        <v-text-field
                            v-model="passwordData.password_confirmation"
                            label="Atkārtot jauno paroli"
                            type="password"
                        />
                        <v-alert
                            v-if="passwordAlert.show"
                            :type="passwordAlert.type"
                            @click:close="passwordAlert.show = false"
                        >
                            {{ passwordAlert.message }}
                        </v-alert>
                        <div class="d-flex justify-end mt-4">
                            <v-btn
                                color="accent"
                                @click="changePassword"
                                class="text-small letter-spacing-0"
                            >
                                Mainīt paroli
                            </v-btn>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

export default {
    props: {
        modelValue: {
            type: Boolean,
            default: false
        }
    },

    emits: ['update:modelValue', 'updated'],

    data() {

        return {
            activeSection: 'profile',
            authStore: useAuthStore(),
            profileAlert: {
                show: false,
                type: 'success',
                message: ''
            },
            passwordAlert: {
                show: false,
                type: 'success',
                message: ''
            },
            profileData: {
                name: '',
                surname: '',
                email: '',
                phone_number: ''
            },

            passwordData: {
                current_password: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    computed: {
        user() {
            return this.authStore.user
        },
        dialog: {
            get() {
                return this.modelValue
            },

            set(value) {
                this.$emit('update:modelValue', value)
            }
        }
    },
    watch: {
        dialog(value) {
            if (value && this.user) {
                this.profileData = {
                    name: this.user.name || '',
                    surname: this.user.surname || '',
                    email: this.user.email || '',
                    phone_number: this.user.phone_number || ''
                }
            }
        },  
    },
    methods: {
        closeDialog() {
            profileAlert = {
                show: false,
                type: 'success',
                message: ''
            },
            passwordAlert = {
                show: false,
                type: 'success',
                message: ''
            }

            this.dialog = false
        },
        // Profila informācijas maiņa
        async saveProfile() {
            try {
                await axios.put(
                    '/api/profile',
                    this.profileData,
                    {
                        withCredentials: true
                    }
                )

                await this.authStore.fetchProfile()

                this.profileAlert = {
                    show: true,
                    type: 'success',
                    message: 'Profila informācija veiksmīgi atjaunota!'
                }
                this.$emit('updated')

            } catch (error) {
                console.error(error)
                this.profileAlert = {
                    show: true,
                    type: 'error',
                    message: error.response?.data?.message || 'Profila informāciju neizdevās atjaunot!'
                }
            }
        },
        // Paroles maiņa
        async changePassword() {
            try {
                await axios.put(
                    '/api/profile/password',
                    this.passwordData,
                    {
                        withCredentials: true
                    }
                )

                this.passwordAlert = {
                    show: true,
                    type: 'success',
                    message: 'Parole veiksmīgi atjaunota!'
                }

                this.passwordData = {
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                }

            } catch (error) {
                console.error(error)
                this.passwordAlert = {
                    show: true,
                    type: 'error',
                    message:  error.response?.data?.message || 'Neizdevās nomainīt paroli.'
                }
            }
        }
    },
    async mounted() {
        try {
        await this.authStore.fetchProfile()
        } catch (err) {
        this.error = 'Neizdevās ielādēt profilu'
        }
    },
}
</script>