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
            height="450"
            cover
            class="group-image"
            alt="img_kolektīvs"
          />
          <v-card-text class="leader-section">

            <div class="leader-chips">
              <v-chip
                v-for="leader in leaders"
                :key="leader.id"
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
        <v-row class="mt-8" dense>

          <!-- DANCERS -->
          <v-col cols="12" md="6">

            <v-card class="h-100">

              <v-card-title>
                Kolektīva dejotāji
              </v-card-title>

              <v-divider></v-divider>

              <div
                v-for="dancer in group.dancers"
                :key="dancer.id"
                class="mb-4"
              >
                <v-card-text>
                  {{ dancer.user.name }} {{ dancer.user.surname }}
                </v-card-text>

                <v-card-subtitle>
                  {{ dancer.age_group.name }}
                  ({{ dancer.age_group.age_group }})
                </v-card-subtitle>
              </div>

              <v-alert
                v-if="!group.dancers?.length"
                type="info"
                variant="tonal"
                class="ma-4"
              >
                Nav dejotāju
              </v-alert>

            </v-card>

          </v-col>

          <!-- ADMISSIONS -->
          <v-col cols="12" md="6">
          <v-card class="h-100">
            <v-card-title class="text-h6">
              Aktīvās uzņemšanas
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text>
              <div
                v-for="ageGroup in group.age_groups"
                :key="ageGroup.id"
                class="mb-6"
              >
                <div
                  v-for="admission in ageGroup.admissions"
                  :key="admission.id"
                  class="mb-4"
                >
                  <v-chip
                    variant="tonal"
                    class="py-6 px-4 text-wrap bg-softblue"
                    style="font-weight: 500; font-size: 0.95rem;"
                  >
                    {{ admission.name }} 
                    <br>
                    <span style="font-size: 0.85rem; font-weight: 400; color: rgba(0,0,0,0.6);" class="ml-2">
                      ({{ admission.age_group.name }}{{ admission.age_group.age_group }})
                    </span>
                  </v-chip>

                  <v-card-subtitle
                    class="text-text text-medium-emphasis mt-2 mb-0"
                    style="line-height: 1.2;"
                  >
                    no <strong>{{ admission.start_date }}</strong> līdz <strong>{{ admission.end_date }}</strong>
                  </v-card-subtitle>
                </div>
              </div>

              <v-alert
                v-if="!group.age_groups?.some(g => g.admissions?.length)"
                type="info"
                variant="tonal"
                class="mt-4"
              >
                Nav aktīvu uzņemšanu
              </v-alert>
            </v-card-text>
</v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
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