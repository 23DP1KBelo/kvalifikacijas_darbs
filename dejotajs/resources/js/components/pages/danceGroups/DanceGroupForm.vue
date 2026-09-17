<template>
  <v-container fluid class="ma-0 pa-0">
    <h2 class="text-accent text-center text-accents mt-6">
      IZVEIDO SAVU KOLEKTĪVU !
    </h2>
    <v-row
      justify="center"
      class="mt-8"
      v-if="step === 1"
    >
      <v-col
        cols="12"
        sm="10"
        md="8"
        lg="6"
        xl="5"
      >
      <!-- Formas lauki -->
        <v-form ref="form" class="fast-fail">
          <v-text-field 
            label="Kolektīva nosaukums" 
            v-model="name"
            :rules="nameRules" 
            required>
          </v-text-field>
          <div class="d-flex flex-row">
            <v-text-field
              class="mr-2" 
              label="Pilsēta" 
              v-model="city" 
              :rules="cityRules"
              required>
            </v-text-field>
            <v-text-field 
              label="Adrese" 
              v-model="address"
              :rules="addressRules" 
              required>
            </v-text-field>
          </div>
          <v-select 
            label="Žanrs" 
            :items="genres" 
            item-title="title" 
            item-value="value"
            :rules="genreRules" 
            v-model="selectedGenre" 
          />
          <v-textarea 
            label="Apraksts" 
            v-model="description" 
            :rules="descriptionRules"
            required>
          </v-textarea>
          <!-- Pārslēgt lapas -->
          <div class="d-flex justify-end mt-6">
            <v-btn
              class="bg-accent text-small"
              @click="nextStep"
            >
              Turpināt
              <v-icon end>mdi-arrow-right</v-icon>
            </v-btn>
          </div>
        </v-form>
      </v-col>
    </v-row>
    <v-row
      v-if="step === 2"
      justify="center"
      class="mt-8"
    >
      <v-col
        cols="12"
        sm="10"
        md="9"
        lg="8"
      >
        <v-row>
          <!-- Attēla ievietošana -->
          <v-col cols="12" md="6">
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
          </v-col>
          <!-- Dokumenta ievietošana -->
          <v-col cols="12" md="6">
            <div
              class="border rounded-lg pa-8 text-center h-100"
              @dragover.prevent
              @drop.prevent="handleApprovalDrop"
            >
              <v-icon size="50" class="mb-4">
                mdi-file-document-plus
              </v-icon>
              <h3 class="text-h6 mb-2">
                Apstiprinājuma dokuments
              </h3>
              <p class="text-body-2 mb-5">
                Ievelc dokumentu šeit
                <br>
                vai izvēlies failu
              </p>
              <v-file-input
                v-model="approval"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                label="Izvēlēties dokumentu"
                variant="outlined"
                prepend-icon=""
                :rules="approvalRules"
                hide-details
              />
            </div>
          </v-col>
        </v-row>
        <!-- Pogas -->
        <div class="d-flex justify-center ga-4 mt-8">
          <v-btn
            variant="outlined"
            @click="step = 1"
          >
          <v-icon start>mdi-arrow-left</v-icon>
            Atpakaļ
          </v-btn>
          <v-btn
            class="bg-accent text-small"
            @click="submitForm"
          >
            Reģistrēt kolektīvu
          </v-btn>
        </div>
        <!-- Paziņojums par apstiprinājumu -->
        <v-dialog
          v-model="danceFormDialog"
          max-width="500"
        >
          <v-card class="pa-4">
            <v-card-title class="text-h6 text-center text-small">
              Kolektīvs ir reģistrēts!
            </v-card-title>

            <v-card-text class="text-center text-wrap text-para">
              Kolektīvs reģistrēts, gaidiet administrātora apstiprinājumu!
            </v-card-text>

            <v-card-actions class="justify-center">
              <v-btn
                class="bg-accent"
                @click=" this.$router.push('/profile')"
              >
                Labi
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  name: "DanceGroupForm",

  data() {
    return {
      step: 1,
      danceFormDialog: false,
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
      ],
      nameRules: [
        v => !!v || 'Kolektīva nosaukums ir obligāts lauks'
      ],
      cityRules: [
        v => !!v || 'Pilsēta ir obligāts lauks'
      ],
      addressRules: [
        v => !!v || 'Adrese ir obligāts lauks'
      ],
      genreRules: [
        v => !!v || 'Lūdzu, izvēlieties žanru'
      ],
      cityRules: [
        v => !!v || 'Pilsēta ir obligāts lauks'
      ],
      descriptionRules: [
        v => !!v || 'Apraksta lauks ir obligāts lauks'
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
      approvalRules: [
        value => {
          return !!value || 'Kolektīva esamības apliecinājums ir obligāts.'
        }
      ],

    }
  },

  methods: {
    async nextStep() {
        const { valid } = await this.$refs.form.validate()

        if (valid) {
            this.step = 2
        }
    },
     handlePictureDrop(event) {
      const file = event.dataTransfer.files[0]

      if (file && file.type.startsWith('image/')) {
        this.picture = file
      }
    },

    handleApprovalDrop(event) {
      const file = event.dataTransfer.files[0]

      if (file) {
        this.approval = file
      }
    },
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

        this.danceFormDialog = true

      } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
          this.error = Object.values(err.response.data.errors)
                      .flat()
                      .join('\n');
          console.log(this.error)
        } else if (err.response && err.response.data && err.response.data.message) {
          this.error = err.response.data.message;
          console.log(this.error)
        } else {
          this.error = 'Nezināma kļūda. Mēģiniet vēlreiz.';
          console.log(this.error)
        }
      }
  }

}
}
</script>

