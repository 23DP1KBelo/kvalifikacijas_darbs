<template>
  <v-container class="pa-4" fluid>
    <v-row justify="center" class="mb-4">
      <v-col cols="12" md="8">
        <div class="d-flex align-center justify-center ">
          <h1 class="font-weight-bold text-primary mb-0 mx-3">
            {{ group.name }}
          </h1>
          <v-btn icon variant="text" @click="openEditDialog()">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
        </div>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card class="pb-8">
          <v-img v-if="group.picture_url" :src="group.picture_url" class="white--text align-end" height="300px" cover></v-img>
          <v-card-text class="d-flex flex-wrap justify-start mt-2">
            <v-chip v-for="leader in leaders" :key="leader.id" class="ma-1 bg-primary" outlined>
              {{ leader.user.name }} {{ leader.user.surname }}
            </v-chip>
          </v-card-text>
          <v-card-text class="mt-2">
            {{ group.description }}
          </v-card-text>
          <v-card-subtitle class="text-subtitle-1">
            {{ group.city }}, {{ group.address }}
          </v-card-subtitle>
        </v-card>
      </v-col>
    </v-row>
    <v-dialog v-model="editDialog" max-width="500px">
      <v-card>
        <v-card-title class="text-h5">Rediģēt kolektīvu</v-card-title>
        <v-card-text>
          <v-form ref="editForm">
            <v-text-field label="Virsraksts" v-model="editGroupData.name" required></v-text-field>
            <v-textarea label="Apraksts" v-model="editGroupData.description" required></v-textarea>
            <v-text-field label="Pilsēta" v-model="editGroupData.city" required></v-text-field>
            <v-text-field label="Adrese" v-model="editGroupData.address" required></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="editDialog = false">Atcelt</v-btn>
          <v-btn color="primary" @click="submitEdit">Saglabāt</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios'
export default{
    data() {
  return {
    group: {},
    leaders: [],
    editDialog: false,
    editGroupData: {
      name: '',
      description: '',
      city: '',
      address: '',
    }
  }
},
methods: {
  async fetchGroup() {
    try {
      const res = await axios.get(`/api/dance-group-info/${this.$route.params.id}`, { withCredentials: true })
      this.group = res.data.data
      this.leaders = this.group.leaders
      console.log(this.group)
    } catch (err) {
      console.error('Kļūda ielādējot deju grupu:', err)
      this.group = null
    }
  },

  openEditDialog() {
    this.editGroupData = {
      name: this.group.name,
      description: this.group.description,
      city: this.group.city,
      address: this.group.address,
    }
    this.editDialog = true
  },

  async submitEdit() {
    try {
      const res = await axios.put(`/api/danceGroups/${this.$route.params.id}`, this.editGroupData, { withCredentials: true })
      this.group = res.data 
      this.editDialog = false
      alert('Kolektīvs veiksmīgi atjaunināts')
      this.fetchGroup()
    } catch (err) {
      console.error('Kļūda atjauninot kolektīvu:', err.response?.data || err)
      alert('Neizdevās atjaunināt kolektīvu')
    }
  }
},
mounted() {
    this.fetchGroup()
}
}
</script>

<style scoped>
h1 {
  font-size: 2rem;
}

@media (max-width: 600px) {
  h1 {
    font-size: 1.5rem;
  }
  .v-card-text {
    font-size: 0.9rem;
  }
}
</style>
