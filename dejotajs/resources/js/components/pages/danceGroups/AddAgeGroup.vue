<template>
    <v-dialog 
        max-width="520"
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
    >
        <v-card class="pa-5">
            <h3 class="text-small text-accent text-center ma-5"> Vecuma grupa</h3>
             <v-form class="ga-2" fast-fail>
                <v-text-field 
                    label="Nosaukums" 
                    v-model="name"
                    :rules="nameRules" 
                    required>
                </v-text-field>
                <v-text-field 
                    label="Vecuma grupa" 
                    v-model="age_group" 
                    :rules="ageGroupRules"
                    required>
                </v-text-field>
                <v-alert 
                    v-if="error" 
                    type="error" 
                    dense
                    outlined 
                    class="mt-3"
                >
                    {{ error }}
                </v-alert>
                <div class="d-flex justify-center align-center text-center mb-4">
                    <v-btn 
                        class="mt-4" 
                        color="accent" 
                        @click="createAgeGroup()">
                        Izveidot
                    </v-btn>
                </div>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios';
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
  data() {
    return {
        name: '',
        age_group: '',
        error: '',
        groupId: this.$route.params.id,
        nameRules: [
            v => !!v || 'Grupas nosaukums ir obligāts lauks'
        ],
        ageGroupRules: [
            v => !!v || 'Vecuma grupa ir obligāts lauks'
        ],
    }
  },
  methods: {
    async createAgeGroup() {
        const groupId = this.$route.params.id;

        if (!groupId) return;

        try {
            await axios.post('/api/ageGroups/create', {
            name: this.name,
            age_group: this.age_group,
            dance_group_id: groupId
            }, {
            withCredentials: true,
            headers: {
                'Content-Type': 'application/json'
            }
            });

            this.$emit('update:modelValue', false);
            this.$emit('updated');

        } catch (err) {
            this.error =
            err.response?.data?.message ||
            'Kļūda saglabājot vecuma grupu';
        }
    }
  }
}
</script>
