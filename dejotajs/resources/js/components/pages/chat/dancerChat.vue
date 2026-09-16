<template>
    <v-btn @click="goTo('/danceForm')"> Kļūt par vadītāju</v-btn>
</template>


<script>
export default {
    methods: {
        goTo(route) {
            this.$router.push(route)
            this.drawer = false
        },

    }
}

</script>
