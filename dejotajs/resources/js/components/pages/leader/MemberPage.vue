<template> 
    <v-container class="py-8"> 
        <v-btn
            variant="text"
            @click="this.$router.back()"
        >
            <v-icon>mdi-arrow-left</v-icon>
        </v-btn>
        <div class="d-flex justify-center flex-column mb-10">
            <h1 class="text-center text-accents">
                KOLEKTĪVA DALĪBNIEKI
            </h1>

            <p class="text-subhed text-center">
                {{ group.name }}
            </p>
        </div>
        <div
            class="d-flex justify-space-between align-center cursor-pointer"
            @click="showMore = !showMore"
        >
            <div class="d-flex align-center">
                <h2 class="text-small mb-0">
                    PAZIŅOJUMI
                </h2>
                <!-- PAZIŅOJUMU SKAITS -->
                <v-badge
                    v-if="notificationCount > 0"
                    :content="notificationCount"
                    color="red"
                    inline
                    class="ml-3"
                />
            </div>

            <v-icon>
                {{ showMore ? 'mdi-chevron-up' : 'mdi-chevron-down' }}
            </v-icon>
        </div>
        <v-expand-transition>
            <div v-if="showMore" class="pa-4">
                <!-- DEJOTĀJA PAZIŅOJUMS -->
                <v-alert
                    v-for="dancer in waitingDancers"
                    :key="dancer.id"
                    color="accent"
                    variant="outlined"
                    class="mb-4"
                >
                    <div class="d-flex justify-space-between align-start">
                        <!-- INFORMĀCIJA -->
                        <div>
                            <v-alert-title class="mb-3">
                                Dejotāja apstiprināšana
                            </v-alert-title>
                            <div class="mb-2 text-small">
                                Vārds, uzvārds:
                                {{ dancer.user.name }}
                                {{ dancer.user.surname }}
                            </div>
                            <div class="mb-2">
                                Tālrunis:
                                {{ dancer.user.phone_number || 'Nav norādīts' }}
                            </div>
                            <div>
                                E-pasts:
                                {{ dancer.user.email }}
                            </div>
                        </div>
                        <!-- POGAS -->
                        <div class="d-flex flex-column ga-2 ml-6">

                            <v-btn
                                color="green"
                                variant="tonal"
                                @click="openAgeGroupDialog(dancer)"
                            >
                                Apstiprināt
                            </v-btn>
                            <v-btn
                                color="red"
                                variant="tonal"
                                @click="updateStatus(dancer, 'decline')"
                            >
                                Noraidīt
                            </v-btn>
                        </div>
                    </div>
                </v-alert>
                <!-- VADĪTĀJU APSTIPRINĀŠANA -->
                <v-alert
                    v-for="leader in leaders"
                    :key="leader.id"
                    color="orange"
                    variant="outlined"
                    class="mb-4"
                >
                    <div class="d-flex justify-space-between align-start">
                        <!-- INFORMĀCIJA -->
                        <div>
                            <v-alert-title class="mb-3">
                            Vadītāja apstiprināšana
                            </v-alert-title>
                            <div class="mb-2 text-small">
                                Vārds, uzvārds:
                                {{ leader.user.name }}
                                {{ leader.user.surname }}
                            </div>
                            <div class="mb-2">
                                Tālrunis:
                                {{ leader.user.phone_number || 'Nav norādīts' }}
                            </div>
                            <div>
                                E-pasts:
                                {{ leader.user.email }}
                            </div>
                        </div>
                        <!-- POGAS -->
                        <div class="d-flex flex-column ga-2 ml-6">

                            <v-btn
                                color="green"
                                variant="tonal"
                                @click="updateLeaderStatus(leader, 'approve')"
                            >
                                Apstiprināt
                            </v-btn>
                            <v-btn
                                color="red"
                                variant="tonal"
                                @click="updateLeaderStatus(leader, 'decline')"
                            >
                                Noraidīt
                            </v-btn>
                        </div>
                    </div>
                </v-alert>
                <!-- NAV PAZIŅOJUMU -->
                <v-alert
                    v-if="this.waitingDancers.length + this.leaders.length == 0"
                    type="info"
                    variant="tonal"
                    class="text-para"
                >
                    Nav atrasti paziņojumi
                </v-alert>
                <!-- VECUMA GRUPAS IZVĒLE -->
                <v-dialog
                    v-model="openAgeGroup"
                    max-width="500"
                >
                    <v-card 
                        class="pa-4"
                        v-if="selectedDancer"
                    >
                        <v-card-title>
                            Dejotāja apstiprināšana
                        </v-card-title>
                        <v-card-text>
                            <div class="mb-2">
                                <strong>
                                    {{ selectedDancer?.user?.name }}
                                    {{ selectedDancer?.user?.surname }}
                                </strong>
                            </div>
                            <v-select
                                v-model="selectedDancer.selectAgeGroup"
                                label="Izvēlies vecuma grupu"
                                :items="ageGroups"
                                :item-title="item => `${item.name} (${item.age_group})`"
                                item-value="id"
                                variant="outlined"
                                density="comfortable"
                            />
                        </v-card-text>
                        <v-card-actions class="justify-end">
                            <v-btn
                                variant="text"
                                @click="openAgeGroup = false"
                            >
                                Atcelt
                            </v-btn>
                            <v-btn
                                color="green"
                                variant="tonal"
                                @click="approveDancer"
                            >
                                Apstiprināt
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </div>
        </v-expand-transition>
        <v-text-field
            v-model="memberSearch"
            label="Meklēt dalībnieku"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="comfortable"
            clearable
            class="mb-4 mt-6"
            @input="searchMembers"
        />
        <!-- MEKLĒŠANAS REZULTĀTI -->
        <div
            v-if="memberSearch && searchResults.length > 0"
            class="mb-8"
        >
            <v-table>
                <thead>
                    <tr>
                        <th class="bg-softblue text-para border">
                            Vārds
                        </th>
                        <th class="bg-softblue text-para border">
                            Uzvārds
                        </th>

                        <th class="bg-softblue text-para border">
                            E-pasts
                        </th>
                        <th class="bg-softblue text-para border">
                            Grupa
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="member in searchResults"
                        :key="member.id"
                        class="border-3"
                    >
                        <td class="border text-small">
                            <v-btn
                                icon="mdi-trash-can-outline"
                                variant="text"
                                size="x-small"
                                @click="openDeleteDialog(member)"
                            >
                            </v-btn>
                            {{ member.user.name }}
                        </td>
                        <td class="border text-small">
                            {{ member.user.surname }}
                        </td>
                        <td class="border text-small">
                            {{ member.user.email }}
                        </td>
                        <td class="border text-small">
                            <span v-if="member.age_group?.name">
                                {{ member.age_group.name }}
                                ({{ member.age_group.age_group }})
                            </span>

                            <span v-else>
                                Vadītājs
                            </span>
                        </td>
                    </tr>
                </tbody>
            </v-table>
        </div>
        <div
            v-else-if="memberSearch && searchResults.length === 0"
            class="text-center mb-8"
        >
            Nav atrasti dalībnieki.
        </div>
        <v-divider class="my-6"></v-divider>
        <!-- Vadītāju tabulas -->
        <div class="d-flex justify-center mb-3">
            <h3 class="text-small align-center">Kolektīva vadītāji</h3>
        </div>
        <v-table
            class="mb-6 pb-6"
        > 
            <thead> 
                <tr> 
                    <th class="bg-accent text-para border">Vārds</th> 
                    <th class="bg-accent text-para border">Uzvārds</th> 
                    <th class="bg-accent text-para border">E-pasts</th>
                    <th class="bg-accent text-para border">Tālruņa numurs</th> 
                </tr>
            </thead> 
            <tbody> 
                <tr 
                    v-for="leader in group.leaders" 
                    :key="leader.id" 
                    class="border-3" 
                > 
                    <td class="border text-small">
                        <v-btn
                            icon="mdi-trash-can-outline"
                            variant="text"
                            size="x-small"
                            @click="openDeleteDialog(leader)"
                        >
                        </v-btn>
                        {{ leader.user.name }}
                    </td> 
                    <td class="border text-small">{{ leader.user.surname }}</td> 
                    <td class="border text-small">{{ leader.user.email }}</td> 
                    <td class="border text-small">{{ leader.user.phone_number}}</td>
                </tr> 
            </tbody> 
        </v-table> 
        <div 
            v-if="group.dancers && group.dancers.length === 0" 
            class="text-center py-6" 
        > 
            Kolektīvam nav apstiprinātu dejotāju. 
        </div> 
        <!-- DEJOTĀJU TABULAS PA GRUPĀM -->
        <div class="d-flex justify-center">
            <v-icon class="mb-6 mr-6">mdi-printer</v-icon>
            <h3 class="text-small align-center">Kolektīva dejotāji</h3>
        </div>
        <v-table> 
            <thead> 
                <tr> 
                    <th class="bg-accent text-para border">Vārds</th> 
                    <th class="bg-accent text-para border">Uzvārds</th> 
                    <th class="bg-accent text-para border">E-pasts</th>
                    <th class="bg-accent text-para border">Grupa</th> 
                </tr>
            </thead> 
            <tbody> 
                <tr 
                    v-for="dancer in group.dancers" 
                    :key="dancer.id" 
                    class="border-3" 
                > 
                    <td class="border text-small">
                        <v-btn
                            icon="mdi-trash-can-outline"
                            variant="text"
                            size="x-small"
                            @click="openDeleteDialog(dancer)"
                        >
                        </v-btn>
                        {{ dancer.user.name }}
                    </td> 
                    <td class="border text-small">{{ dancer.user.surname }}</td> 
                    <td class="border text-small">{{ dancer.user.email }}</td> 
                    <td class="border text-small">{{ dancer.age_group.name }} ({{ dancer.age_group.age_group }})</td>
                </tr> 
            </tbody> 
        </v-table> 
        <div 
            v-if="group.dancers && group.dancers.length === 0" 
            class="text-center py-6" 
        > 
            Kolektīvam nav apstiprinātu dejotāju. 
        </div> 
    </v-container>
    <v-dialog
        v-model="deleteDialog"
        max-width="500"
    >
        <v-card class="pa-4">
        <v-card-title class="text-h6 text-center">
            {{ group.name }}
        </v-card-title>

        <v-card-text class="text-center text-wrap text-para">
            Vai vēlies dzēst šo kolektīva dalībnieku?
        </v-card-text>

        <v-card-actions class="justify-center">
            <v-btn
            variant="outlined"
            @click="deleteMember()"
            >
            Jā
            </v-btn>

            <v-btn
            class="bg-accent"
            @click="deleteDialog = false"
            >
            Nē
            </v-btn>
        </v-card-actions>
        </v-card>
    </v-dialog> 
</template>
<script>
import axios from 'axios'

export default {
    data() {
        return {
            group: {
                dancers: [],
                leaders: [],
            },
            deleteDialog: false,
            waitingDancers: [],
            selectedMember: null,
            leaders: [],
            ageGroups: [],
            showMore: false,
            openAgeGroup: false,
            selectedDancer: null,
            memberSearch: '',
            searchResults: [],
        }
    },
    computed: {
        notificationCount() {
            return this.waitingDancers.length + this.leaders.length
        }
    },
    methods: {
        openDeleteDialog(member) {
            this.selectedMember = member
            this.deleteDialog = true
        },
         async deleteMember() {
            try {
                await axios.delete(
                    `/api/delete-member/${this.selectedMember.id}`,
                    {
                    withCredentials: true
                    }
                )

                this.deleteDialog = false
                this.selectedLeader = false

                await this.fetchGroup()
                this.searchMembers()


                } catch (err) {
                console.error(
                    'Kļūda dzēšot dalībnieku:',
                    err.response?.data || err
                )

                alert('Neizdevās dzēst dalībnieku')
                }
        },
        fetchLeaders() {
            axios
                .get(`/api/approval-leader/${this.group.id}`, { withCredentials: true })
                .then((res) => {
                this.leaders = Array.isArray(res.data.data) ? res.data.data : [];
                })
                .catch((err) => console.log(err));
        },
        updateLeaderStatus(leader, action) {
            const url = action === "approve" ? "/api/approval-leader" : "/api/decline-leader";
            axios
                .post(
                url,
                {
                    id: leader.id,
                    role: leader.role,
                    user_id: leader.user.id,
                    dance_group_id: leader.dance_group.id,
                },
                { withCredentials: true }
                )
                .then(() => {
                this.leaders = this.leaders.filter((l) => l.id !== leader.id);
                this.fetchGroup();
                })
                .catch((err) => console.error(err.response?.data || err));
        },
        openAgeGroupDialog(dancer) {
            this.selectedDancer = dancer
            this.openAgeGroup = true
        },
        async fetchGroup() {
            try {
                const res = await axios.get(`/api/dance-group-info/${this.$route.params.id}`, { withCredentials: true })
                this.group = res.data.data
                this.ageGroups = this.group.age_groups || [];
            } catch (err) {
                this.group = null
            }
        },
        fetchDancers() {
            axios
                .get(`/api/dancers/${this.group.id}`, { withCredentials: true })
                .then((res) => {
                    this.waitingDancers = (res.data.data || []).map(dancer => ({
                        ...dancer,
                        selectAgeGroup: null,
                        error: '' 
                    }));
                })
                .catch((err) => 
                    console.log(err)
                );
        },
        async approveDancer() {
            if (!this.selectedDancer?.selectAgeGroup) {
                this.selectedDancer.error =
                    'Lūdzu, izvēlieties vecuma grupu pirms apstiprināšanas.'
                return
            }

            try {
                await this.updateStatus(this.selectedDancer, 'approve')

                this.openAgeGroup = false
                this.selectedDancer = null
            } catch (err) {
                console.error(err)
            }
        },
        async searchMembers() {
            clearTimeout(this.memberSearchTimeout)

            if (!this.memberSearch) {
                this.searchResults = []
                return
            }

            this.memberSearchTimeout = setTimeout(async () => {
                try {
                    const res = await axios.get(
                        `/api/members/${this.group.id}/search`,
                        {
                            params: {
                                q: this.memberSearch
                            },
                            withCredentials: true
                        }
                    )
                    this.searchResults = res.data.data || []

                } catch (err) {
                    console.error(
                        'Kļūda meklējot dalībniekus:',
                        err.response?.data || err
                    )
                    this.searchResults = []
                }
            }, 300)
        },
        async updateStatus(dancer, action) {
            const url = action === "approve"
                ? "/api/dancers/approval"
                : "/api/dancers/decline"

            if (action === "approve" && !dancer.selectAgeGroup) {
                dancer.error =
                    'Lūdzu, izvēlieties vecuma grupu pirms apstiprināšanas.'
                return
            }

            try {
                await axios.post(
                    url,
                    {
                        id: dancer.id,
                        role: dancer.role,
                        user_id: dancer.user.id,
                        age_group_id: dancer.selectAgeGroup,
                        dance_group_id: dancer.dance_group.id,
                    },
                    {
                        withCredentials: true
                    }
                )

                this.waitingDancers = this.waitingDancers.filter(
                    d => d.id !== dancer.id
                )
                await this.fetchGroup();

            } catch (err) {
                console.error(err.response?.data || err)
                throw err
            }
        },
    },
    async mounted() {
        await this.fetchGroup()
        this.fetchDancers()
        this.fetchLeaders()
    },
}
</script>
