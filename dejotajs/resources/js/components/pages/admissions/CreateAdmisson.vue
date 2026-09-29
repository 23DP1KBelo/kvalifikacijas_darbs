<template>
    <v-dialog
        max-width="520"
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
    >
        <v-card elevation="16" >
            <v-card-title class="text-small text-accent text-center">Jauna uzņemšana</v-card-title>
            <v-card-text>
                <v-form 
                    ref="form" 
                    v-model="valid"
                    fall-first
                >
                <v-row class="mb-4" dense>
                    <!-- Nosaukums -->
                    <v-col cols="12" md="12">
                    <v-text-field
                        v-model="name"
                        label="Nosaukums"
                        variant="outlined"
                        density="comfortable"
                        :rules="nameRules"
                    ></v-text-field>
                    </v-col>

                    <!-- Sākuma datums -->
                    <v-col cols="12" md="6">
                    <v-menu
                        v-model="menuStart"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template #activator="{ props }">
                        <v-text-field
                            v-model="startDate"
                            label="Sākuma datums"
                            prepend-icon="mdi-calendar"
                            readonly
                            :rules="dateRules"
                            v-bind="props"
                        ></v-text-field>
                        </template>
                        <v-date-picker
                        v-model="startDate"
                        @update:modelValue="menuStart = false"
                        ></v-date-picker>
                    </v-menu>
                    </v-col>
                    <!-- Beigu datums -->
                    <v-col cols="12" md="6">
                    <v-menu
                        v-model="menuEnd"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template #activator="{ props }">
                        <v-text-field
                            v-model="endDate"
                            label="Beigu datums"
                            prepend-icon="mdi-calendar"
                            readonly
                            :rules="dateRules"
                            v-bind="props"
                        ></v-text-field>
                        </template>
                        <v-date-picker
                        v-model="endDate"
                        @update:modelValue="menuEnd = false"
                        ></v-date-picker>
                    </v-menu>
                    </v-col>

                    <!-- Vecuma grupas -->
                    <v-col cols="12" md="12">
                    <v-select
                        v-model="selectedAgeGroup"
                        :items="ageGroups"
                        :item-title="ageGroup => `${ageGroup.name} (${ageGroup.age_group})`"
                        item-value="id"
                        label="Izvēlies deju grupu"
                        variant="outlined"
                        density="comfortable"
                        clearable
                        :rules="ageGroupRules"
                        prepend-icon="mdi-dance-ballroom"
                    ></v-select>
                    </v-col>
                </v-row>
                <v-alert v-if="error" type="error" dense outlined class="mt-3">
                    {{ error }}
                </v-alert>
                <v-btn color="primary" @click="submitForm">
                    Saglabāt
                </v-btn>
                </v-form>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios'

export default {
    props: {
        modelValue: {
            type: Boolean,
            default: false
        },

        group: {
            type: Object,
            required: true
        }
    },

    emits: [
        'update:modelValue',
        'updated'
    ],

    data() {
        return {
            valid: false,
            name: '',
            error: '',
            startDate: null,
            endDate: null,
            menuStart: false,
            menuEnd: false,
            selectedAgeGroup: null,
            ageGroups: [],
            nameRules: [
                v => !!v || 'Kolektīva nosaukums ir obligāts lauks'
            ],
            dateRules: [
                v => !!v || 'Datums ir obligāts lauks'
            ],
            ageGroupRules: [
                v => !!v || 'Izvēlieties vecuma grupu'
            ],
        }
    },

    watch: {
        group: {
            immediate: true,
            handler(newGroup) {
                this.ageGroups = newGroup?.age_groups || []
            }
        }
    },

    methods: {
        formatDate(date) {
            if (!date) return null

            const d = new Date(date)
            const month = (d.getMonth() + 1)
                .toString()
                .padStart(2, '0')

            const day = d.getDate()
                .toString()
                .padStart(2, '0')

            const year = d.getFullYear()

            return `${year}-${month}-${day}`
        },

        async submitForm() {
            this.error = ''

            if (
                !this.name ||
                !this.startDate ||
                !this.endDate ||
                !this.selectedAgeGroup
            ) {
                this.error =
                    'Lūdzu aizpildiet visus laukus un izvēlieties vecuma grupu.'

                return
            }

            const payload = {
                name: this.name,
                start_date: this.formatDate(this.startDate),
                end_date: this.formatDate(this.endDate),
                age_group_id: Number(this.selectedAgeGroup),
                dance_group_id: this.group.id
            }

            try {
                await axios.post( '/api/admission/create', payload,{withCredentials: true})

                this.$emit('update:modelValue', false)
                this.$emit('updated')

                this.name = ''
                this.startDate = null
                this.endDate = null
                this.selectedAgeGroup = null

            } catch (err) {
                console.log(err.response?.data)

                if (err.response?.data?.errors) {
                    this.error = Object
                        .values(err.response.data.errors)
                        .flat()
                        .join(' ')
                } else if (err.response?.data?.message) {
                    this.error = err.response.data.message
                } else {
                    this.error =
                        'Nezināma kļūda. Mēģiniet vēlreiz.'
                }
            }
        }
    }
}
</script>