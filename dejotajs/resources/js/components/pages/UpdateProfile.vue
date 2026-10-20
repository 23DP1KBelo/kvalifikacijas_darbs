<template>
    <v-dialog
        v-model="dialog"
        max-width="800"
        width="100%"
        persistent
    >
        <v-card>
            <!-- Virsraksts -->
            <div>
                <div class="d-flex justify-start mt-4 ml-4">
                    <v-btn
                        variant="text"
                        icon="mdi-arrow-left"
                        aria-label="Aizvērt iestatījumus"
                        @click="closeDialog"
                    />
                </div>

                <h2 class="text-accent text-center ma-4">
                    IESTATĪJUMI
                </h2>
            </div>

            <v-divider />

            <v-row no-gutters>
                <!-- Kreisā navigācija -->
                <v-col
                    cols="12"
                    md="3"
                    class="border-e"
                >
                    <v-list nav>
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

                        <v-list-item
                            :active="activeSection === 'deleteProfile'"
                            @click="activeSection = 'deleteProfile'"
                        >
                            <template #prepend>
                                <v-icon color="error">
                                    mdi-delete-outline
                                </v-icon>
                            </template>

                            <v-list-item-title class="text-para">
                                Profila dzēšana
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-col>

                <!-- Labā satura daļa -->
                <v-col
                    cols="12"
                    md="9"
                    class="pa-5"
                >
                    <div class="settings-content">
                        <!-- PROFILS -->
                        <div v-if="activeSection === 'profile'">
                            <h4 class="text-accent mb-5 text-center">
                                Profils
                            </h4>

                            <v-form @submit.prevent="saveProfile">
                                <div class="d-flex flex-row ga-3">
                                    <v-text-field
                                        v-model="profileData.name"
                                        label="Vārds"
                                        autocomplete="given-name"
                                    />

                                    <v-text-field
                                        v-model="profileData.surname"
                                        label="Uzvārds"
                                        autocomplete="family-name"
                                    />
                                </div>

                                <v-text-field
                                    v-model="profileData.email"
                                    label="E-pasts"
                                    type="email"
                                    autocomplete="email"
                                />

                                <v-text-field
                                    v-model="profileData.phone_number"
                                    label="Tālrunis"
                                    autocomplete="tel"
                                />

                                <v-alert
                                    v-if="profileAlert.show"
                                    :type="profileAlert.type"
                                    closable
                                    class="mb-4"
                                    @click:close="profileAlert.show = false"
                                >
                                    {{ profileAlert.message }}
                                </v-alert>

                                <div class="d-flex justify-end mt-4">
                                    <v-btn
                                        type="submit"
                                        color="accent"
                                        :loading="savingProfile"
                                        :disabled="savingProfile"
                                    >
                                        Saglabāt
                                    </v-btn>
                                </div>
                            </v-form>
                        </div>

                        <!-- DROŠĪBA -->
                        <div v-else-if="activeSection === 'security'">
                            <h4 class="text-accent mb-5 text-center">
                                Paroles maiņa
                            </h4>

                            <v-form @submit.prevent="changePassword">
                                <v-text-field
                                    v-model="passwordData.current_password"
                                    label="Pašreizējā parole"
                                    type="password"
                                    autocomplete="current-password"
                                />

                                <v-text-field
                                    v-model="passwordData.password"
                                    label="Jaunā parole"
                                    type="password"
                                    autocomplete="new-password"
                                />

                                <v-text-field
                                    v-model="passwordData.password_confirmation"
                                    label="Atkārtot jauno paroli"
                                    type="password"
                                    autocomplete="new-password"
                                />

                                <v-alert
                                    v-if="passwordAlert.show"
                                    :type="passwordAlert.type"
                                    closable
                                    class="mb-4"
                                    @click:close="passwordAlert.show = false"
                                >
                                    {{ passwordAlert.message }}
                                </v-alert>

                                <div class="d-flex justify-end mt-4">
                                    <v-btn
                                        type="submit"
                                        color="accent"
                                        :loading="changingPassword"
                                        :disabled="changingPassword"
                                    >
                                        Mainīt paroli
                                    </v-btn>
                                </div>
                            </v-form>
                        </div>

                        <!-- PROFILA DZĒŠANA -->
                        <div v-else-if="activeSection === 'deleteProfile'">
                            <v-card
                                variant="tonal"
                                class="pa-4"
                            >
                                <div class="d-flex justify-center mb-3">
                                        <v-icon
                                            size="34"
                                            color="white"
                                        >
                                            mdi-account-remove-outline
                                        </v-icon>
                                </div>

                                <v-card-title class="text-h6 text-center text-wrap">
                                    Dzēst {{ user?.name || '' }}
                                    {{ user?.surname || '' }} profilu?
                                </v-card-title>

                                <v-card-text class="text-center text-wrap">
                                    Vai tiešām vēlaties dzēst savu profilu?
                                    Šī darbība var būt neatgriezeniska.
                                    Pēc dzēšanas jūs tiksiet izrakstīts no konta.
                                </v-card-text>

                                <v-alert
                                    v-if="deleteAlert.show"
                                    :type="deleteAlert.type"
                                    closable
                                    class="mb-4"
                                    @click:close="deleteAlert.show = false"
                                >
                                    {{ deleteAlert.message }}
                                </v-alert>

                                <v-card-actions class="justify-center flex-wrap ga-3">
                                    <v-btn
                                        color="error"
                                        variant="flat"
                                        :loading="deletingProfile"
                                        :disabled="deletingProfile"
                                        @click="deleteProfile"
                                    >
                                        Jā, dzēst profilu
                                    </v-btn>

                                    <v-btn
                                        variant="outlined"
                                        :disabled="deletingProfile"
                                        @click="closeDialog"
                                    >
                                        Atcelt
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
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
            authStore: useAuthStore(),

            activeSection: 'profile',

            savingProfile: false,
            changingPassword: false,
            deletingProfile: false,

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

            deleteAlert: {
                show: false,
                type: 'error',
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
            if (value) {
                this.resetAlerts()
                this.loadProfileData()
            }
        }
    },

    methods: {
        resetAlerts() {
            this.profileAlert.show = false
            this.passwordAlert.show = false
            this.deleteAlert.show = false
        },

        loadProfileData() {
            if (!this.user) return

            this.profileData = {
                name: this.user.name || '',
                surname: this.user.surname || '',
                email: this.user.email || '',
                phone_number: this.user.phone_number || ''
            }
        },

        closeDialog() {
            this.resetAlerts()
            this.dialog = false
        },

        // Profila informācijas maiņa
        async saveProfile() {
            this.savingProfile = true
            this.profileAlert.show = false

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
                console.error('Profila atjaunošanas kļūda:', error)

                this.profileAlert = {
                    show: true,
                    type: 'error',
                    message: error.response?.data?.message
                        || 'Profila informāciju neizdevās atjaunot!'
                }
            } finally {
                this.savingProfile = false
            }
        },

        // Paroles maiņa
        async changePassword() {
            this.changingPassword = true
            this.passwordAlert.show = false

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
                    message: 'Parole veiksmīgi nomainīta!'
                }

                this.passwordData = {
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                }
            } catch (error) {
                console.error('Paroles maiņas kļūda:', error)

                this.passwordAlert = {
                    show: true,
                    type: 'error',
                    message: error.response?.data?.message
                        || 'Neizdevās nomainīt paroli.'
                }
            } finally {
                this.changingPassword = false
            }
        },

        // Profila dzēšana
        async deleteProfile() {
            if (this.deletingProfile) return

            const userId = this.user?.id

            if (!userId) {
                this.deleteAlert = {
                    show: true,
                    type: 'error',
                    message: 'Neizdevās atrast lietotāja informāciju. Lūdzu, ielogojieties vēlreiz.'
                }
                return
            }

            this.deletingProfile = true
            this.deleteAlert.show = false

            try {
                // Laravel maršrutam jābūt:
                // DELETE /api/profile/delete/{user}
                await axios.delete(
                    `/api/profile/delete/${userId}`,
                    {
                        withCredentials: true
                    }
                )

                // Aizveram dialogu
                this.dialog = false

                // Notīrām lokālo autentifikācijas stāvokli
                // Ja store ir logout metode, izmantojam to.
                if (typeof this.authStore.logout === 'function') {
                    await this.authStore.logout()
                } else {
                    this.authStore.user = null
                }

                // Pārejam uz login lapu
                if (this.$route.path !== '/login') {
                    await this.$router.replace('/login')
                }
            } catch (error) {
                console.error(
                    'Profila dzēšanas kļūda:',
                    error.response?.data || error
                )

                this.deleteAlert = {
                    show: true,
                    type: 'error',
                    message: error.response?.data?.message
                        || 'Neizdevās dzēst profilu. Lūdzu, mēģiniet vēlreiz.'
                }
            } finally {
                this.deletingProfile = false
            }
        }
    },

    async mounted() {
        try {
            await this.authStore.fetchProfile()
            this.loadProfileData()
        } catch (error) {
            console.error('Neizdevās ielādēt profilu:', error)
        }
    }
}
</script>

<style scoped>
.settings-content {
    width: 100%;
    max-width: 450px;
    margin-inline: auto;
}

.settings-content :deep(.v-btn) {
    letter-spacing: 0;
}

@media (max-width: 600px) {
    .settings-content .d-flex.flex-row {
        flex-direction: column !important;
        gap: 0 !important;
    }
}
</style>
