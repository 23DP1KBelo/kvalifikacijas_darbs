<template>
     <v-container class="d-flex justify-center align-center mt-8">
        <v-card class=" bg-softblue" elevation="16" width="500px">
            <v-card-title class="text-center mb-4 mt-4">Uzņemšana</v-card-title>
            <v-card-text>
                <v-form ref="form" v-model="valid">
                <v-row class="mb-4" dense>

                    <!-- Nosaukums -->
                    <v-col cols="12" md="12">
                    <v-text-field
                        v-model="name"
                        label="Nosaukums"
                        variant="outlined"
                        density="comfortable"
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
                            v-bind="props"
                        ></v-text-field>
                        </template>
                        <v-date-picker
                        v-model="endDate"
                        @update:modelValue="menuEnd = false"
                        ></v-date-picker>
                    </v-menu>
                    </v-col>

                    <!-- Vecuma grupas  select -->
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
    </v-container>
</template>

<script>
import axios from 'axios'

export default {
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
            ageGroups: []
        }
    },
    methods: {
         formatDate(date) {
            if (!date) return null;
            const d = new Date(date);
            const month = (d.getMonth() + 1).toString().padStart(2, '0');
            const day = d.getDate().toString().padStart(2, '0');
            const year = d.getFullYear();
            return `${year}-${month}-${day}`; // "YYYY-MM-DD"
        },

        async submitForm() {
    // reset error
    this.error = '';

    // 1️⃣ Check all required fields on front-end
    if (!this.name || !this.startDate || !this.endDate || !this.selectedAgeGroup) {
        this.error = 'Lūdzu aizpildiet visus laukus un izvēlieties vecuma grupu.';
        return;
    }

    // 2️⃣ Prepare payload
    const payload = {
        name: this.name,
        start_date: this.formatDate(this.startDate),
        end_date: this.formatDate(this.endDate),
        age_group_id: Number(this.selectedAgeGroup),
        dance_group_id: this.group.id
    };

    console.log('Submitting:', payload);

    try {
        const response = await axios.post('/api/admission/create', payload, { withCredentials: true });
        alert('Uzņemšana ir sākta!');
        this.$router.push('/');
    } catch (err) {
        console.log(err.response?.data);

        if (err.response?.data?.errors) {
            this.error = Object.values(err.response.data.errors).flat().join(' ');
        } else if (err.response?.data?.message) {
            this.error = err.response.data.message;
        } else {
            this.error = 'Nezināma kļūda. Mēģiniet vēlreiz.';
        }
    }
},
        async fetchGroup() {
            try {
                const res = await axios.get(`/api/dance-group-info/${this.$route.params.id}`, { withCredentials: true })
                this.group = res.data.data
                this.leaders = this.group.leaders
                this.ageGroups = this.group.age_groups

                if (!this.ageGroups.length) {
                    this.error = "Nav pieejamu vecuma grupu";
                }
                console.log(this.ageGroups)
            } catch (err) {
                console.error('Kļūda ielādējot deju grupu:', err)
                this.group = null
            }
        },
    },
    mounted(){
        this.fetchGroup()
    }
}
</script>
