<template>
    <h1 class="text-center mt-8">Deju kolektīvi</h1>
    <v-container>
        <v-card v-for="group in groups" :key="group.id" class="mb-4 py-4">
            <v-card-title>{{ group.name }}</v-card-title>
            <v-card-subtitle>{{ group.city }} , {{ group.address }}</v-card-subtitle>
        </v-card>
    </v-container>
</template>

<script>
import axios from 'axios'

export default{
    data(){
        return {
            groups: []
        }
    },
    methods: {
        fetchDanceGroups(){
            axios.get('/danceGroups-all')
        .then(res => {
          this.groups = Array.isArray(res.data.data) ? res.data.data : [];
        })
        .catch(err => console.log(err));
        }
    },
    mounted(){
        this.fetchDanceGroups()
    }
}
</script>
