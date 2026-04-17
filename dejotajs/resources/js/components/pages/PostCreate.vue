<template>
    <v-container class="d-flex justify-center align-center mt-8">
        <v-card class=" bg-softblue" elevation="16" width="500px">
            <v-card-title class="text-center mb-4 mt-4">Ierakstu izveide</v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field label="Nosaukums" v-model="title" required></v-text-field>
                    <v-text-field label="Apraksts" v-model="description" required></v-text-field>
                    <v-file-input label="Attēls" v-model="picture" accept="image/*"/>
                    <v-checkbox label="Privāts ieraksts" v-model="privatePost"></v-checkbox>
                    <v-alert v-if="error" type="error" dense outlined class="mt-3">
                        {{ error }}
                    </v-alert>
                    <div class="d-flex justify-center align-center text-center mb-4">
                        <v-btn class="mt-4" color="primary" @click="submitForm">
                            Pievienot ierakstu
                        </v-btn>
                    </div>
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
      title: '',
      description: '',
      picture: null,
      privatePost: false,
      error: ''
    }
  },
  methods: {
    async submitForm() {
      try {
        const groupId = this.$route.params.id 

        const formData = new FormData()
        formData.append('title', this.title)
        formData.append('description', this.description)
        if (this.picture) {
        formData.append('picture', this.picture) // tas tiek sūtīts uz serveri
        }
        formData.append('private', this.privatePost ? '1' : '0')
        formData.append('dance_group_id', groupId)

        await axios.post('/api/posts', formData, {
          withCredentials: true,
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        this.$router.push(`/group/${groupId}`)

      } catch (err) {
        this.error = err.response?.data?.message || 'Kļūda izveidojot ierakstu'
      }
    }
  }
}
</script>