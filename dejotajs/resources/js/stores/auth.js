import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        danceGroupMembers: [],
        loading: true,
    }),

    getters: {
        // vai lietotājs ir pieslēdzies
        isAuthenticated: (state) => {
            return !!state.user
        },

        // Sistēmas administrators
        isAdmin: (state) => {
            return state.user?.role === 'admin'
        },

        // Ir vismaz vienā kolektīvā apstiprināts kā vadītājs
        isLeader: (state) => {
            return state.danceGroupMembers.some(member =>
                member.role === 'leader' &&
                member.status === 'approved'
            )
        },

        // Ir vismaz vienā kolektīvā apstiprināts kā dejotājs
        isDancer: (state) => {
            return state.danceGroupMembers.some(member =>
                member.role === 'dancer' &&
                member.status === 'approved'
            )
        },

        // Visi apstiprinātie kolektīvi
        approvedGroups: (state) => {
            return state.danceGroupMembers
                .filter(member => member.status === 'approved')
                .map(member => member.dance_group)
        },

        // Kolektīvi, kuros lietotājs ir vadītājs
        leaderGroups: (state) => {
            return state.danceGroupMembers
                .filter(member =>
                    member.role === 'leader' &&
                    member.status === 'approved'
                )
                .map(member => member.dance_group)
        },

        // Kolektīvi, kuros lietotājs ir dejotājs
        dancerGroups: (state) => {
            return state.danceGroupMembers
                .filter(member =>
                    member.role === 'dancer' &&
                    member.status === 'approved'
                )
                .map(member => member.dance_group)
        },
    },

    actions: {
        async fetchUser() {
            try {
                const response = await axios.get('/api/profile', {
                    withCredentials: true,
                })

                this.user = response.data.user
                this.danceGroupMembers =
                    response.data.dance_group_members || []

            } catch (error) {
                this.user = null
                this.danceGroupMembers = []
            } finally {
                this.loading = false
            }
        },

        async logout() {
            try {
                await axios.post('/logout', {}, {
                    withCredentials: true,
                })
            } finally {
                this.user = null
                this.danceGroupMembers = []
            }
        },
    },
})