<template>
    <h1 class="text-center mt-8">Deju kolektīvi</h1>
    <v-container class="d-flex justify-center mb-4">
        <v-col cols="12" sm="6" md="4">
        <v-text-field
            v-model="searchQuery"
            label="Meklēt kolektīvu"
            prepend-inner-icon="mdi-magnify"
            clearable
            dense
            outlined
        ></v-text-field>
        </v-col>
    </v-container>
    <v-container fluid>
    <v-row dense>
        <v-col v-for="group in groups" :key="group.id" cols="12" sm="4">
        <v-card class="mx-8 my-4 bg-secondary" height="250px">
            <v-card-title @click="goTo(`/group-info/${group.id}`)" style="cursor: pointer;" class="clickable-title">
            {{ group.name }}
            </v-card-title>
            <v-card-subtitle>{{ group.city }}, {{ group.address }}</v-card-subtitle>
            <v-img
            v-if="group.picture_url"
            :src="group.picture_url"
            height="150px"
            width="100%"
            class="mb-2 mt-2"
            contain
            ></v-img>
        </v-card>
        </v-col>
    </v-row>
    </v-container>
</template>

<script>
import axios from 'axios'

export default{
    data(){
        return {
            groups: [],
            searchQuery: ''
        }
    }, 
    methods: {
        fetchDanceGroups(){
            axios.get('/danceGroups-all')
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : [];
        })
        .catch(err => console.log(err));
        },
        goTo(route) { 
            this.$router.push(route); 
        }
    },
    mounted(){
        this.fetchDanceGroups()
    }
}
</script>

<style scoped>
.clickable-title:hover {
  text-decoration: underline;
}
</style>