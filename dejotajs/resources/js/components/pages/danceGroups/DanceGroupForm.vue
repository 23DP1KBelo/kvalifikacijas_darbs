<template>
     <v-container class="d-flex justify-center align-center mt-8">
        <v-card class=" bg-softblue" elevation="16" width="500px">
            <v-card-title class="text-center mb-4 mt-4">Kolektīva reģistrēšanāna</v-card-title>
            <v-card-text>
                <v-form>
                    <v-text-field label="Kolektīva nosaukums" v-model="name" required></v-text-field>
                    <v-textarea label="Apraksts" v-model="description" required></v-textarea>
                    <v-text-field label="Pilsēta" v-model="city" required></v-text-field>
                    <v-text-field label="Adrese" v-model="address" required></v-text-field>
                    <v-select label="Žanrs" :items="genres" item-title="title" item-value="value" v-model="selectedGenre" variant="outlined"/>
                    <v-file-input label="Augšupielādē kolektīva attēlu" v-model="picture" accept="image/*"/>
                    <v-file-input label="Augšupielādē kolektīva dokumentu" v-model="approval" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required/>
                    <v-alert v-if="error" type="error" dense outlined class="mt-3">
                        {{ error }}
                    </v-alert>
                    <div class="d-flex justify-center align-center text-center mb-4">
                      <v-btn class="mt-4" color="primary" @click="submitForm">
                          Reģistrēt
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
  name: "DanceGroupForm",

  data() {
    return {
      name: "",
      description: "",
      city: "",
      address: "",
      selectedGenre: null,
      picture: null,
      approval: null,
      error: '',
      genres: [
          { title: "Liriskās dejas", value: "lyrical dance" },
          { title: "Balets", value: "ballet" },
          { title: "Mūsdienīgās dejas", value: "contemporary dance" },
          { title: "Tautas dejas", value: "folk dance" },
          { title: "Hip-hop", value: "hip hop" },
          { title: "Cits", value: "other" }
      ]
    }
  },

  methods: {
    async submitForm() {
      try {
        const formData = new FormData()

        formData.append('name', this.name)
        formData.append('description', this.description)
        formData.append('city', this.city)
        formData.append('address', this.address)
        formData.append('genre', this.selectedGenre)

        if (this.picture) {
        formData.append('picture', this.picture)
        }

        if (this.approval) {
        formData.append('approval', this.approval)
        }


        const response = await axios.post(
          'api/danceGroups',
          formData,
          { withCredentials: true }
        )

        alert("Kolektīvs veiksmīgi reģistrēts!")
        this.$router.push('/')

      } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
          this.error = Object.values(err.response.data.errors).flat().join(' ');
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

