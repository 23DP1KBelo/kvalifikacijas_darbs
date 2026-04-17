<template>
  <v-container class="wrapper">

    <!-- Header -->
    <div class="header text-center">
      <h1 class="title">
        Atvērtās uzņemšanas
      </h1>

      <p class="subtitle">
        Pievienojies kādā no Latvijas deju kolektīviem
      </p>
    </div>

    <!-- List -->
    <div
      v-for="ageGroup in ageGroups"
      :key="ageGroup.id"
      class="admission-item"
    >

      <v-card
        elevation="2"
        class="group-card bg-softblue"
      >

        <!-- Group info -->
        <div class="group-info">
          <div class="group-name">
            {{ ageGroup.dance_group.name }}
          </div>

          <div class="group-age">
            {{ ageGroup.name }} ({{ ageGroup.age_group }})
          </div>
        </div>

        <!-- Admissions -->
        <div class="chips">

          <v-chip
            v-for="admission in ageGroup.admissions"
            :key="admission.id"
            size="large"
            class="admission-chip bg-primary text-white"
          >
            <div class="chip-inner">

              <v-card-title class="chip-name">
                {{ admission.name }}
              </v-card-title>

              <v-card-text class="chip-date">
                no {{ admission.start_date }} līdz {{ admission.end_date }}
              </v-card-text>

            </div>
          </v-chip>

        </div>

        <!-- Leaders -->
        <div class="leaders">
            <v-card-text>
                Lai pievienoties deju kolektīvam sazinies ar kolektīva vadītāju/iem: 
            </v-card-text>
          <v-card-text
            v-for="leader in ageGroup.leaders"
            :key="leader.id"
            class="mt-0 pt-0"
          >
            {{ leader.user.name }} {{ leader.user.surname }}: {{ leader.user.email }}
          </v-card-text>
        </div>

      </v-card>

    </div>

  </v-container>
</template>

<script>
import axios from 'axios'

export default{
    data(){
        return {
            ageGroups: []
        }    
    },
    methods: {
        async fetchAgeGroups() {
             try {
                  const res = await axios.get('/api/admission-age-groups')
                  this.ageGroups = res.data.data
                  console.log(this.ageGroups)
                } catch (err) {
                  console.error('Kļūda ielādējot deju grupu:', err)
                  this.group = null
            }
        },
    },
    mounted(){
        this.fetchAgeGroups()
    }
}
</script>

<style scoped>

.wrapper {
  max-width: 900px;
}

/* HEADER */

.header {
  margin-bottom: 40px;
}

.title {
  font-size: 36px;
  font-weight: 700;
}

.subtitle {
  opacity: 0.7;
  margin-top: 6px;
}

/* LIST */

.admission-item {
  margin-bottom: 24px;
}

/* CARD */

.group-card {
  padding: 24px;
  border-radius: 16px;
  transition: 0.2s;
}

.group-card:hover {
  transform: translateY(-2px);
}

/* GROUP INFO */

.group-info {
  margin-bottom: 16px;
}

.group-name {
  font-size: 20px;
  font-weight: 600;
}

.group-age {
  opacity: 0.7;
  font-size: 14px;
}

/* ADMISSIONS */

.chips {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 16px;
}

.admission-chip {
  justify-content: flex-start;
  padding: 12px 16px;
  height: auto;
  border-radius: 12px;
}

/* CHIP CONTENT */

.chip-inner {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.chip-name {
  font-weight: 600;
}

.chip-date {
  font-size: 13px;
  opacity: 0.8;
}

/* LEADERS */

.leaders {
  display: flex;
  flex-direction: column;
  gap: 4px;
  font-size: 13px;
  opacity: 0.7;
}

.leader {
  line-height: 1.4;
}

/* RESPONSIVE */

@media (max-width: 600px) {

  .wrapper {
    padding-left: 12px;
    padding-right: 12px;
  }

  .title {
    font-size: 28px;
  }

  .group-card {
    padding: 18px;
  }

}

</style>