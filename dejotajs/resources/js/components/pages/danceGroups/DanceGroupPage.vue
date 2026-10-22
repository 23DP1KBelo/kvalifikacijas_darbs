<template>
  <v-row class="ma-0">
      <!-- Kolektīva informācija -->
      <v-col
        cols="12"
        md="9"
        class="pa-4 d-flex flex-column"
        style="height: 100%;"
      >
      <v-container>
        <div class="d-flex flex-row justify-center">
        <h2 class="text-center text-accents mb-7">
          {{ group.name }}
        </h2>
        <v-btn
          variant="text"
          class="mt-5 ml-2"
          @click="openEditDialog()"
        >
          <v-icon>mdi-pencil-outline</v-icon>
        </v-btn>
      </div>
      <v-img
        :src="group.picture_url"
        width="100%"
        height="230"
        cover
        alt="Kolektīva attēls"
      />
      <!-- Vieta, dalībnieki, žanrs -->
      <div class="d-flex flex-column flex-md-row justify-space-between mt-7 ga-4">
        <div class="mb-3 d-flex flex-row">
          <v-icon>
            mdi-map-marker-outline
          </v-icon>
          <p class="text-small ml-3 ">
            {{ group?.city }}, {{ group?.address }}
          </p>
        </div>
        <!-- Dalībnieki -->
        <div class="d-flex flex-row">
          <v-btn 
            v-if="isLeader"
            @click="this.$router.push(`/members/${this.group.id}`)"
          >
            <v-icon>
              mdi-account-group-outline
            </v-icon>
            <p class="text-small ml-3">
              Dalībnieki: {{ group?.dancers?.length || 0 }}
            </p>
            <v-badge
              v-if="notificationCount > 0"
              :content="notificationCount"
              color="red"
              inline
              class="ml-3"
            />
          </v-btn>
          <div v-else>
            <v-icon>
              mdi-account-group-outline
            </v-icon>
            <p class="text-small ml-3">
              Dalībnieki: {{ group?.dancers?.length || 0 }}
            </p>
          </div>
        </div>
        <div class="d-flex flex-row">
          <v-icon>
            mdi-star-outline
          </v-icon>
          <p class="text-small ml-3">
             {{ translatedGenre || 'Nav norādīts' }}
          </p>
        </div>
      </div>
  
      <div class=" mt-5">
        <div>
          <v-btn
            variant="text"
            class="text-small text-none"
            @click="showMore = !showMore"
          >
            <v-icon>
              {{ showMore ? 'mdi-chevron-down' : 'mdi-chevron-right' }}
            </v-icon>
              <h3 class="text-small text-none">
                Par kolektīvu
              </h3>
          </v-btn>
          <v-divider class="mb-7"/>
          <!--  Informācija par kolektīvu -->
          <v-expand-transition>
            <!-- Izbīdāmā iznformācija -->
            <div v-if="showMore" class="pa-4">
              <!-- Apraksts -->
              <div>
                <p class="text-para text-center">
                  {{ group.description}}
                </p>
              </div>
              <v-divider class="mt-7 mb-7"/>
              <!-- Vadītāji -->
              <div>
                <h3 class="text-small">
                  Vadītāji:
                </h3>
                <div class="d-flex flex-wrap ga-2 mt-7 text-small">
                  <v-chip
                    v-for="leader in group?.leaders"
                    :key="leader.user.id"
                    variant="outlined"
                    style="border-color: #02317A;"
                    class="bg-white pa-7"
                  >
                    {{ leader.user.name }} {{ leader.user.surname }}
                  </v-chip>

                  <v-chip
                    v-if="!group?.leaders?.length"
                    variant="tonal"
                    class="mt-7"
                  >
                    Nav vadītāju
                  </v-chip>
                </div>
              </div>
              <v-divider class="mt-7 mb-7"/>
              <!-- Vecuma grupas -->
              <div>
                <h3 class="text-small">
                  Vecuma grupas
                </h3>
                <div class="d-flex justify-end">
                  <v-btn
                    v-if="isLeader"
                    rounded="lg"
                    class="bg-accent mt-6 text-none text-small letter-spacing-0"
                    @click="addAgeGroup = true"
                  >
                    Pievienot grupu
                  </v-btn>
                </div>
                <div class="d-flex flex-wrap ga-2">
                  <v-chip 
                    v-for="ageGroup in group?.age_groups || []"
                    :key="ageGroup.id"
                    variant="outlined"
                    style="border-color: #02317A;"
                    class="bg-white text-small mt-7 pa-4"
                  >
                    {{ ageGroup.name }}: {{ ageGroup.age_group }}
                  </v-chip>

                  <v-chip
                    class="mt-7 text-small pa-4"
                    v-if="!(group?.age_groups?.length)"
                  >
                    Nav vecuma grupu
                  </v-chip>
                </div>
              </div>
              <v-divider class="mt-7 mb-7"/>
              <!-- Uzņemšanu apraksti -->
              <div>
                <h3 class="text-small">
                  Kolektīva uzņemšanas
                </h3>
                <!-- Pievienot uzņemšanu -->
                <div class="d-flex justify-end">
                  <v-btn
                    v-if="isLeader"
                    rounded="lg"
                    class="bg-accent mt-6 text-none text-small letter-spacing-0"
                    @click="admissonForm = true"
                  >
                    Pievienot uzņemšanu
                  </v-btn>
                </div>
                <div
                  v-if="group?.age_groups?.some( ageGroup => ageGroup.admissions?.length)"
                  class="d-flex flex-wrap ga-3"
                >
                  <template
                    v-for="ageGroup in group.age_groups"
                    :key="ageGroup.id"
                  >
                    <v-card
                      v-for="admission in ageGroup.admissions"
                      :key="admission.id"
                      variant="outlined"
                      rounded="lg"
                      class="bg-primary elevation-3 mb-8 pa-6 pa-md-8 mx-auto d-flex flex-column justify-center mt-7"
                      width="700"
                    >
                      <h3 class="text-small text-center">
                        {{ admission.name }} ( {{ ageGroup.name }} {{ ageGroup.age_group }})
                      </h3>
                      <div class="text-para text-center mt-3">
                        <h4>
                          {{ admission.start_date }} -
                          {{ admission.end_date }}
                        </h4>
                      </div>
                    </v-card>
                  </template>
                </div>
                <v-chip
                  v-else
                  class="mt-7 text-small pa-4"
                >
                  Nav pieejamu uzņemšanu
                </v-chip>
              </div>
              <v-divider class="mt-7 mb-7"/>
              </div>
          </v-expand-transition>
          <!-- Kolektīva raksti -->
          <div class="d-flex flex-row justify-center align-center">
            <h2 class="text-center text-accents mb-7 mr-7">
              KOLEKTĪVA RAKSTI
            </h2>
            <v-menu>
              <template #activator="{ props }">
                <v-btn
                  v-bind="props"
                  icon
                  variant="text"
                >
                  <v-icon>mdi-filter-variant</v-icon>
                </v-btn>
              </template>

              <v-list>
                <v-list-item @click="postFilter = 'all'; currentPage = 1">
                  <v-list-item-title>Visi raksti</v-list-item-title>
                </v-list-item>

                <v-list-item @click="postFilter = 'public'; currentPage = 1">
                  <v-list-item-title>Publiskie</v-list-item-title>
                </v-list-item>

                <v-list-item @click="postFilter = 'private'; currentPage = 1">
                  <v-list-item-title>Privātie</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
          <div class="d-flex justify-end">
            <v-btn
              v-if="isLeader"
              rounded="lg"
              class="bg-accent mt-6 text-none text-small letter-spacing-0"
              @click="addPost = true"
            >
              Pievienot ierakstu
            </v-btn>
          </div>
          <v-row class="mt-4">
            <v-col
              v-for="post in paginatedPosts"
              :key="post.id"
              cols="12"
              sm="6"
              md="4"
            >
              <v-card
                variant="outlined"
                rounded="lg"
                height="220"
                class="overflow-hidden"
                @click="openArticle(post)"
              >
                <!-- Ja ir bilde -->
                <v-img
                  v-if="post.picture"
                  :src="post.picture"
                  height="100%"
                  cover
                  alt="Posta attēls"
                />

                <!-- Ja nav bildes -->
                <div
                  v-else
                  class="h-100 d-flex align-center justify-center pa-4"
                >
                  <h3 class="text-center text-small">
                    {{ post.title }}
                  </h3>
                </div>
              </v-card>
              <v-dialog
                v-model="dialog"
                max-width="1100"
                scrim="rgba(0, 0, 0, 0.4)"
              >
                <v-card
                  class="rounded-0 order-last order-md-first"
                  min-height="600"
                  color="white"
                  elevation="0"
                >
                  <v-row no-gutters class="h-100">
                    <v-col
                      cols="12"
                      md="6"
                      class="d-flex flex-column pa-8"
                    >
                      <!-- Atpakaļ poga -->
                      <div class="d-flex justify-start">
                        <v-btn
                          variant="text"
                          prepend-icon="mdi-arrow-left"
                          @click="dialog = false"
                        >
                        </v-btn>
                      </div>
                      <!-- Kolektīva nosaukums -->
                      <h4 class="mt-4">
                        <div 
                          class="text-subtitle-1 text-medium-emphasis text-right"
                        >
                          {{
                            selectedPost?.dance_group_member?.dance_group?.name ||
                            'Nezināms kolektīvs'
                          }}
                        </div>
                      </h4>
                      <div class="flex-grow-1 d-flex align-center py-8">
                        <div class=" text-para text-black">
                          {{ selectedPost?.description || '' }}
                        </div>
                      </div>
                      <div class="text-caption text-medium-emphasis">
                        {{ formatDate(selectedPost?.created_at) }}
                      </div>
                    </v-col>
                    <!-- Labā puse -->
                    <v-col
                      cols="12"
                      md="6"
                      class="d-flex flex-column pa-8 order-first order-md-last"
                    >
                      <!-- Raksta nosaukums, edit poga -->
                       <div class="d-flex flex-row ">
                        <v-btn
                          v-if="isLeader"
                          variant="text"
                          class="mt-5 "
                          @click="openPostEditDialog(selectedPost)"
                        >
                          <v-icon>mdi-pencil-outline</v-icon>
                        </v-btn>
                        <h2 class="text-accent  mt-6 text-uppercase text-center">
                          {{ selectedPost?.title }}
                        </h2>
                       </div>
                      <!-- Bilde -->
                      <v-img
                        :src="
                          selectedPost?.picture ||
                          `https://picsum.photos/id/${selectedPost?.id}/600/350`
                        "
                        width="100%"
                        height="350"
                        contain
                        alt="Raksta_attels"
                      />
                    </v-col>
                  </v-row>
                </v-card>
              </v-dialog>
            </v-col>
          </v-row>
          <v-pagination
            v-if="pageCount > 1"
            v-model="currentPage"
            :length="pageCount"
            :total-visible="5"
            rounded="circle"
            class="mt-6"
          />
        </div>
      </div>
      </v-container>
      </v-col>  
      <!-- Saziņas informācija -->
      <v-col
        cols="3"
        class="d-none d-md-block pa-4"
        style="height: 100%;"
      >
        <div class="h-100">
          <h2 class="text-accent text-center text-accents">
            SAZINIES!
          </h2>
        </div> 
      </v-col>
    </v-row>
    <EditDanceGroupDialog
      v-model="editDialog"
      :group="group"
      @updated="fetchGroup"
      @deleted="$router.push('/profile')"
    />
    <AddAgeGroup
      v-model="addAgeGroup"
      :group="group"
      @updated="fetchGroup"
    />
    <AdmissonForm
      v-model="admissonForm"
      :group="group"
      @updated="fetchGroup"
    />
    <AddPost
      v-model="addPost"
      :group="group"
      @created="fetchPosts"
    />
    <!-- Ieraksta rediģēšana -->
    <v-dialog 
      v-model="editPostDialog" 
      max-width="560px"
    >
      <v-card class="dialog-card">
        <div class="d-flex flex-row text-center justify-center align-center">
          <h2 class=" text-small text-accent text-center mt-3">
            Rediģēt ierakstu
          </h2>
          <v-btn
            v-if="isLeader"
            variant="text"
            color="error"
            class="mt-4"
            @click="deletePostDiolog = true"
          >
            <v-icon>mdi-trash-can-outline</v-icon>
          </v-btn>
        </div>

        <v-card-text class="px-6">
          <v-form 
            ref="editForm"
            fall-first
          >
            <v-text-field
              label="Virsraksts"
              v-model="editPostData.title"
              rounded="lg"
              :rules="titleRules"
              maxlenght="255"
              required
            />

            <v-textarea
              label="Apraksts"
              v-model="editPostData.description"
              rounded="lg"
              rows="5"
              maxlenght="500"
              :rules="descriptionRules"
              required
            />
          </v-form>
        </v-card-text>

        <v-card-actions class="px-6 pb-6">
          <v-spacer />
          <v-btn 
            variant="text" 
            @click="editPostDialog = false"
            class="text-small text-none"
          >
            Atcelt
          </v-btn>

          <v-btn 
            class=" bg-accent text-white text-small text-none" 
            @click="submitPostEdit"
          >
            Saglabāt
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Ieraksta dzēšana -->
    <v-dialog
      v-model="deletePostDiolog"
      max-width="560px"
    >
      <v-card class="pa-4">
        <v-card-title class="text-h6 text-center">
          Dzēst ierakstu
        </v-card-title>

        <v-card-text class="text-center text-wrap text-para">
          Vai vēlies dzēst šo ierakstu?
        </v-card-text>

        <v-card-actions class="justify-center">
          <v-btn
            variant="outlined"
            @click="deletePost(selectedPost.id)"
          >
            Jā
          </v-btn>

          <v-btn
            class="bg-accent"
            @click="deletePostDiolog = false"
          >
            Nē
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>


<script>
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import EditDanceGroupDialog from './EditDanceGroupDialog.vue'
import AddAgeGroup from '../ageGroups/CreateAgeGroup.vue'
import AdmissonForm from '../admissions/CreateAdmisson.vue'
import AddPost from '../posts/CreatePosts.vue'

const genreMap = {
  'lyrical dance': 'Liriskā deja',
  'contemporary dance': 'Mūsdienīgās dejas',
  'ballet': 'Balets',
  'hip hop': 'Hip-hops',
  'folk dance': 'Tautas dejas',
  'other': 'Cits'
};

export default {
  components: {
    EditDanceGroupDialog,
    AddAgeGroup,
    AdmissonForm,
    AddPost
  },
  name: 'GroupPosts',
  data() {
    return {
      showMore: false,
      postFilter: 'all',
      currentPage: 1,
      postsPerPage: 9,
      editPostDialog: false,
      authStore: useAuthStore(),
      group: {},
      postFilter: 'all',
      editDialog: false,
      admissonForm: false,
      addAgeGroup: false,
      dialog: false,
      addPost: false,
      deletePostDiolog: false,
      selectedPost: null,
      waitingDancers: [],
      leaders: [],
      posts: [],
      editPostData: {
        id: null,
        title: '',
        description: ''
      },
      titleRules: [
          v => !!v || 'Raksta virsraksts ir obligāts lauks'
      ],
      descriptionRules: [
        v => !!v || 'Raksta apraksts ir obligāts lauks'
      ],
    }
  },
  computed: {
    translatedGenre() {
      return this.group?.genre ? (genreMap[this.group.genre] || this.group.genre) : 'Nav norādīts';
    },
    user() {
      return this.authStore.user
    },
    notificationCount() {
      return this.waitingDancers.length + this.leaders.length
    },
    isLeader() {
      if (!this.authStore.user || !this.group?.leaders) {
        return false
      }

      return this.group.leaders.some(
        leader => leader.user.id === this.authStore.user.id
      )
    },
    paginatedPosts() {
      const start = (this.currentPage - 1) * this.postsPerPage
      const end = start + this.postsPerPage

      return this.filteredPosts.slice(start, end)
    },

    pageCount() {
      return Math.ceil(this.filteredPosts.length / this.postsPerPage)
    },
    filteredPosts() {
      if (this.postFilter === 'public') {
        return this.posts.filter(post => post.private === 'publisks')
      }

      if (this.postFilter === 'private') {
        return this.posts.filter(post => post.private === 'Privāts')
      } 
      console.log(this.posts.map(post => post.private))
      return this.posts
    },
  },
  methods: {
    openEditDialog() {
      this.editGroupData = {
        name: this.group.name,
        description: this.group.description,
        city: this.group.city,
        address: this.group.address,
      }
      this.editDialog = true
    },

    openArticle(post) {
      this.selectedPost = post
      this.dialog = true
    },
    async fetchGroup() {
        try {
          const res = await axios.get(`/api/dance-group-info/${this.$route.params.id}`, { withCredentials: true })
          this.group = res.data.data
          this.leaders = this.group.leaders
        } catch (err) {
          this.group = null
        }
    },
    fetchLeaders() {
            axios
                .get(`/api/approval-leader/${this.group.id}`, { withCredentials: true })
                .then((res) => {
                this.leaders = Array.isArray(res.data.data) ? res.data.data : [];
                console.log(this.leaders)
                })
                .catch((err) => console.log(err));
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
              console.log(this.waitingDancers)
          })
          .catch((err) => 
              console.log(err)
          );
    },
    async fetchPosts() {
      try {
        const groupId = this.$route.params.id
        const res = await axios.get(`/api/my-posts/${groupId}`, { withCredentials: true })
        this.group = res.data.dance_group
        this.posts = res.data.posts
        console.log(this.posts)
      } catch (err) {
        if (err.response && err.response.status === 403) {
          this.$router.push('/no-access')
        } else {
          console.error('Kļūda ielādējot postus:', err)
          this.group = {}
          this.posts = []
        }
      }
    },
    async deletePost(postId) {
      try {
        await axios.delete(`/api/posts/${postId}`, { withCredentials: true })
        this.posts = this.posts.filter(post => post.id !== postId)

        this.editPostDialog = false
        this.deletePostDiolog = false
        this.dialog = false

      } catch (err) {
        alert('Neizdevās dzēst ierakstu')
      }
    },
    openPostEditDialog(post) {
      this.editPostData = {
        id: post.id,
        title: post.title,
        description: post.description,
        private: post.private,                  
        dance_group_member_id: post.dance_group_member_id,
        picture: post.picture
      }
      this.editPostDialog = true
    },
    async submitPostEdit() {
    try {
        const response = await axios.put(
            `/api/posts/${this.editPostData.id}`,
            {
                title: this.editPostData.title,
                description: this.editPostData.description
            },
            {
                withCredentials: true
            }
        )
        this.editPostDialog = false
        await this.fetchPosts()
        const updatedPost = this.posts.find(
            post => post.id === this.editPostData.id
        )
        if (updatedPost) {
            this.selectedPost = updatedPost
        }
        } catch (err) {
            console.error('Kļūda rediģējot ierakstu:', err)

            if (err.response?.data?.errors) {
                alert(
                    Object.values(err.response.data.errors)
                        .flat()
                        .join('\n')
                )
            } else if (err.response?.data?.message) {
                alert(err.response.data.message)
            } else {
                alert('Neizdevās atjaunināt ierakstu.')
            }
        }
    },

    formatDate(dateStr) {
      if (!dateStr) return ''

      return new Date(dateStr).toLocaleDateString('lv-LV', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      })
    },

    async deleteDanceGroup() {
    if (!confirm('Vai tiešām vēlaties dzēst šo kolektīvu? Šī darbība ir neatgriezeniska.')) {
      return
    }
    try {
      const id = this.route.params.id
      await axios.delete(`/api/danceGroups/${id}`, { withCredentials: true })
        alert('Kolektīvs veiksmīgi dzēsts')
        this.$router.push('/')
    } catch (err) {
      console.error('Kļūda dzēšot kolektīvu:', err)
      this.$router.push('/')
    }
  },

  },
  async mounted() {
    await this.authStore.fetchProfile()
    await this.fetchPosts()
    await this.fetchGroup()
    this.fetchDancers()
    this.fetchLeaders()
  },
  watch: {
    'route.params.id': function () {
      this.fetchPosts()
    }
  }
}
</script>
