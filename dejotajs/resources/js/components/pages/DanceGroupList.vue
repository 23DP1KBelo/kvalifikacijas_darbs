<template>
    <h1 class="text-center mt-8">Izvēlies kolektīvu:</h1>
    <v-container>
        <v-card v-for="group in groups" :key="group.id" class="mb-8">
            <v-card-title>{{ group.name }}</v-card-title>
            <v-card-text>{{ group.city }} {{ group.address }}</v-card-text>
            <div class="d-flex justify-end">
                <v-btn color="primary" size="small" class="mb-4 mr-6"  @click="joinGroup(group.id)">Pievienoties</v-btn>
            </div>
        </v-card>
    </v-container>
</template>

<script>
import axios from 'axios';

export default {
    data(){
        return{
             groups: []
        }
    },
    mounted(){
        this.fetchDanceGroups();
    },
    methods: {
        fetchDanceGroups(){
            axios.get('/api/danceGroups', {withCredentials: true})
            .then(res => {
                this.groups = Array.isArray(res.data.data) ? res.data.data : [];
                console.log(this.groups);
            })
            .catch(err => console.log(err));
        },
        joinGroup(groupId) {
            axios.post('/api/members', { dance_group_id: groupId }, { withCredentials: true })
            .then(res => {
                alert('Pievienots kolektīvam!');
                window.location.reload();
            })
            .catch(err => {
                console.error(err.response?.data); 
                alert(err.response?.data?.message || 'Neizdevās pievienoties.');
            });
        }
    }
}
</script>
