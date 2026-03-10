<template>
  <v-container class="py-8" fluid>

    <v-row justify="center" class="mb-6">
      <v-col cols="12" md="8">

        <div class="d-flex align-center justify-center flex-wrap">

          <h1 class="group-title">
            {{ group.name }}
          </h1>

          <v-btn
            icon
            variant="text"
            class="ml-2"
            @click="openEditDialog()"
          >
            <v-icon>mdi-pencil</v-icon>
          </v-btn>

        </div>

      </v-col>
    </v-row>
    <v-row justify="center">
      <v-col cols="12" md="8">

        <div class="mb-2 d-flex ">
          <v-btn
            color="primary"
            rounded="xl"
            elevation="2"
            prepend-icon="mdi-plus"
            @click="$router.push(`/age-group-form/${group.id}`)"
          >
            Pievienot vecuma grupu
          </v-btn>
        </div>
        
        <div class="mb-2 d-flex ">
          <v-btn
            color="secondary"
            rounded="xl"
            elevation="2"
            prepend-icon="mdi-plus"
            @click="$router.push(`/admisson/${group.id}`)"
          >
            Pievienot uzņemšanu
          </v-btn>
        </div>
        <v-card class="group-card">
          <v-img
            v-if="group.picture_url"
            :src="group.picture_url"
            height="320"
            cover
            class="group-image"
          />
          <v-card-text class="leader-section">

            <div class="leader-chips">
              <v-chip
                v-for="leader in leaders"
                :key="leader.id"
                color="primary"
                variant="tonal"
                class="ma-1"
              >
                {{ leader.user.name }} {{ leader.user.surname }}
              </v-chip>

              <span v-if="!leaders.length" class="text-medium-emphasis">
                Nav vadītāju
              </span>
            </div>

          </v-card-text>

          <v-card-text class="description-text">
            {{ group.description }}
          </v-card-text>

          <v-card-subtitle class="location-text">
            <v-icon size="18" class="mr-1">mdi-map-marker</v-icon>
            {{ group.city }}, {{ group.address }}
          </v-card-subtitle>

          <v-divider class="my-4"></v-divider>

          <v-card-text>

            <div class="section-label mb-3">
              Vecuma grupas
            </div>

            <v-list density="comfortable">

              <v-list-item
                v-for="ageGroup in group?.age_groups || []"
                :key="ageGroup.id"
              >
                <v-list-item-title>
                  {{ ageGroup.name }}
                </v-list-item-title>

                <v-list-item-subtitle>
                  {{ ageGroup.age_group || 'Nav informācijas' }}
                </v-list-item-subtitle>
              </v-list-item>

              <v-alert
                v-if="!(group?.age_groups?.length)"
                type="info"
                variant="tonal"
                border="start"
              >
                Nav pievienotu vecuma grupu
              </v-alert>

            </v-list>

          </v-card-text>

        </v-card>

      </v-col>
    </v-row>


    <!-- EDIT DIALOG -->
    <v-dialog v-model="editDialog" max-width="520">

      <v-card class="edit-dialog">

        <v-card-title class="text-h6">
          Rediģēt kolektīvu
        </v-card-title>

        <v-card-text>

          <v-form ref="editForm">

            <v-text-field
              label="Nosaukums"
              v-model="editGroupData.name"
              variant="outlined"
              density="comfortable"
              required
            />

            <v-textarea
              label="Apraksts"
              v-model="editGroupData.description"
              variant="outlined"
              density="comfortable"
              rows="3"
              required
            />

            <v-text-field
              label="Pilsēta"
              v-model="editGroupData.city"
              variant="outlined"
              density="comfortable"
              required
            />

            <v-text-field
              label="Adrese"
              v-model="editGroupData.address"
              variant="outlined"
              density="comfortable"
              required
            />

          </v-form>

        </v-card-text>

        <v-card-actions>
          <v-spacer />

          <v-btn variant="text" @click="editDialog = false">
            Atcelt
          </v-btn>

          <v-btn color="primary" @click="submitEdit">
            Saglabāt
          </v-btn>

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

.group-title{
  font-size:36px;
  font-weight:700;
  letter-spacing:0.5px;
}

.group-card{
  border-radius:20px;
  overflow:hidden;
  padding-bottom:12px;
}

.group-image{
  border-bottom-left-radius:20px;
  border-bottom-right-radius:20px;
}

.section-label{
  font-size:14px;
  font-weight:600;
  opacity:0.7;
  margin-bottom:8px;
}

.leader-section{
  padding-top:20px;
}

.leader-chips{
  display:flex;
  flex-wrap:wrap;
}

.description-text{
  font-size:16px;
  line-height:1.6;
  opacity:0.9;
}

.location-text{
  display:flex;
  align-items:center;
  font-size:15px;
  opacity:0.8;
}

.edit-dialog{
  border-radius:16px;
}

</style>