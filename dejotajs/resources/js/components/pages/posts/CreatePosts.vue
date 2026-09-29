<template>
    <v-dialog 
        max-width="520"
        :model-value="modelValue"
        @update:model-value="$emit('update:modelValue', $event)"
    >
        <v-card class="pa-5">
            <h3 class="text-small text-accent text-center ma-5">Raksta izveide</h3>
             <v-form class="ga-2" fast-fail>
                <v-text-field 
                    label="Virsraksts" 
                    v-model="title"
                    :rules="titleRules" 
                    maxlength="255"
                    required>
                </v-text-field>
                <v-textarea
                    label="Apraksts" 
                    v-model="description" 
                    :rules="descriptionRules"
                    maxlength="500"
                    required>
                </v-textarea>
                <v-switch
                    v-model="isPrivate"
                    :true-value="true"
                    :false-value="false"
                    color="accent"
                    hide-details
                >
                    <template #label>
                        <span class="d-flex align-center">
                            <v-icon class="mr-1">
                                {{ isPrivate ? 'mdi-lock' : 'mdi-earth' }}
                            </v-icon>

                            {{ isPrivate ? 'Privāts' : 'Publisks' }}
                        </span>
                    </template>
                </v-switch>
                <div
                    class="border rounded-lg pa-8 text-center h-100"
                    @dragover.prevent
                    @drop.prevent="handlePictureDrop"
                >
                    <v-icon size="50" class="mb-4">
                        mdi-image-plus
                    </v-icon>
                    <h3 class="text-h6 mb-2">
                        Kolektīva attēls
                    </h3>
                    <p class="text-body-2 mb-5">
                        Ievelc attēlu šeit
                        <br>
                        vai izvēlies failu
                    </p>
                    <v-file-input
                        v-model="picture"
                        accept="image/*"
                        label="Izvēlēties attēlu"
                        variant="outlined"
                        :rules="imageRules"
                        prepend-icon=""
                        hide-details
                    />
                </div>
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
                        @click="createPost()">
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
            title: '',
            description: '',
            isPrivate: false,
            picture: null,
            error: null,
            titleRules: [
                v => !!v || 'Raksta virsraksts ir obligāts lauks'
            ],
            descriptionRules: [
                v => !!v || 'Raksta apraksts ir obligāts lauks'
            ],
            imageRules: [
                value => {
                if (!value) return true

                const file = Array.isArray(value) ? value[0] : value

                if (!file) return true

                return file.type.startsWith('image/')
                    || 'Lūdzu, izvēlies attēla formāta failu.'
                }
            ],
        }
    },
    methods: {
        async createPost() {
            try {
                this.error = null;

                const formData = new FormData();

                formData.append('title', this.title);
                formData.append('description', this.description);
                formData.append('private', this.isPrivate ? '1' : '0');
                formData.append('dance_group_id', this.group.id);

                if (this.picture) {
                    const file = Array.isArray(this.picture)
                        ? this.picture[0]
                        : this.picture;

                    if (file) {
                        formData.append('picture', file);
                    }
                }

                await axios.post('/api/posts', formData, {
                    withCredentials: true,
                });

                this.$emit('created');
                this.$emit('update:modelValue', false);

            } catch (error) {
                console.error('Kļūda izveidojot ierakstu:', error);

                this.error =
                    error.response?.data?.message ||
                    'Neizdevās izveidot ierakstu.';
            }
        }
    }
}
   
</script>
