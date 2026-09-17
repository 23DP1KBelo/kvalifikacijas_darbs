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
      <div class="d-flex flex-row justify-space-between mt-7">
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
          <v-icon>
            mdi-account-group-outline
          </v-icon>
          <p class="text-small ml-3">
            Dalībnieki: {{ group?.dancers?.length || 0 }}
          </p>
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
             <!-- Uzņemšanas -->
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
      @click="$router.push('/calender')"
    >
      Pievienot uzņemšanu
    </v-btn>
  </div>

  <!-- Uzņemšanas -->
    <div
      v-if="group?.age_groups?.some(
        ageGroup => ageGroup.admissions?.length
      )"
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

          <!-- Uzņemšanas nosaukums -->
          <h3 class="text-small text-center">
            {{ admission.name }} ( {{ ageGroup.name }} {{ ageGroup.age_group }})
          </h3>
          <!-- Datumi -->
          <div class="text-para text-center mt-3">
            <h4>
              {{ admission.start_date }} -
              {{ admission.end_date }}
            </h4>
          </div>
          <!-- Pievienoties -->
          <div class="d-flex justify-end">
            <v-btn
              rounded="lg"
              class="bg-accent mt-6 text-none text-small letter-spacing-0"
            >
              Pievienoties
            </v-btn>
          </div>
        </v-card>
            </template>
          </div>

              <!-- Nav uzņemšanu -->
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
              >
                <v-card
                  class="rounded-0 order-last order-md-first"
                  min-height="600"
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
                      <!-- Pilnais teksts -->
                      <div class="flex-grow-1 d-flex align-center py-8">
                        <div class=" text-para text-black">
                          {{ selectedPost?.description || '' }}
                        </div>
                      </div>
                      <!-- Datums -->
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
                      <!-- Raksta nosaukums -->
                      <h2 class="text-accent text-accents mt-6 text-uppercase text-center">
                        {{ selectedPost?.title }}
                      </h2>
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
    />
    <AddAgeGroup
      v-model="addAgeGroup"
      :group="group"
      @updated="fetchGroup"
    />
</template>
<!-- <template>
  <v-container fluid class="group-page pa-0">
    <section class="hero-section">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" md="10" lg="8">
            <div class="hero-card">
              <div>
                <p class="text-caption text-uppercase text-secondary mb-1">
                  Kolektīva ieraksti
                </p>
                <h1
                  class="text-h3 font-weight-bold text-primary cursor-pointer"
                  @click="$router.push(`/group-profile/${group.id}`)"
                >
                  {{ group.name || 'Kolektīvs' }}
                </h1>
              </div>
              <div class="hero-actions" v-if="isLeader">
                <v-btn
                  class="bg-primary text-white"
                  rounded="xl"
                  elevation="6"
                  prepend-icon="mdi-plus"
                  @click="$router.push(`/create-post/${group.id}`)"
                >
                  Pievienot ierakstu
                </v-btn>
                <v-btn
                  icon="mdi-delete-outline"
                  variant="tonal"
                  color="secondary"
                  rounded="xl"
                  @click="deleteDanceGroup"
                />
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>
    <v-container class="posts-container">
      <v-row justify="center" v-if="posts.length">
        <v-col
          v-for="post in posts"
          :key="post.id"
          cols="12"
          md="10"
          lg="8"
          class="mb-8"
        >
          <v-card class="post-card" elevation="0">
            <div class="post-image-wrapper">
              <v-img
                :src="post.picture || `https://picsum.photos/id/${post.id}/900/500`"
                height="360"
                cover
                class="post-image"
              />
              <div v-if="isLeader" class="post-actions">
                <v-btn
                  icon="mdi-pencil-outline"
                  variant="flat"
                  size="small"
                  class="text-secondary"
                  @click="openEditDialog(post)"
                />
                <v-btn
                  icon="mdi-delete-outline"
                  variant="flat"
                  size="small"
                  class="text-secondary"
                  @click="deletePost(post.id)"
                />
              </div>
            </div>

            <v-card-text class="pa-7">
              <div class="d-flex justify-space-between align-center flex-wrap ga-2 mb-3">
                <v-chip color="primary" variant="tonal" size="small">
                  {{ post.dance_group_member?.dance_group?.name || group.name || 'Kolektīvs' }}
                </v-chip>

                <span class="text-primary text-medium-emphasis">
                  {{ formatDate(post.created_at) }}
                </span>
              </div>

              <h2 class="text-h5 font-weight-bold mb-3 text-black">
                {{ post.title }}
              </h2>

              <p class="text-body-1 text-black">
                {{ post.description }}
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-row v-else justify="center">
        <v-col cols="12" md="8">
          <v-card class="empty-card text-center pa-10" elevation="0">

            <h2 class="text-h5 font-weight-bold mb-2">
              Šim kolektīvam vēl nav ierakstu
            </h2>

            <p class="text-body-1 text-medium-emphasis mb-6">
              Kad kolektīva vadītājs pievienos ierakstu, tas būs redzams šajā sadaļā.
            </p>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-dialog v-model="editDialog" max-width="560px">
      <v-card class="dialog-card">
        <v-card-title class="text-h5 font-weight-bold px-6 pt-6">
          Rediģēt ierakstu
        </v-card-title>

        <v-card-text class="px-6">
          <v-form ref="editForm">
            <v-text-field
              label="Virsraksts"
              v-model="editPostData.title"
              variant="outlined"
              rounded="lg"
              required
            />

            <v-textarea
              label="Apraksts"
              v-model="editPostData.description"
              variant="outlined"
              rounded="lg"
              rows="5"
              required
            />
          </v-form>
        </v-card-text>

        <v-card-actions class="px-6 pb-6">
          <v-spacer />

          <v-btn variant="text" @click="editDialog = false">
            Atcelt
          </v-btn>

          <v-btn color="primary" rounded="xl" @click="submitEdit">
            Saglabāt
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template> -->

<script>
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import EditDanceGroupDialog from './EditDanceGroupDialog.vue'
import AddAgeGroup from './AddAgeGroup.vue'

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
    AddAgeGroup
  },
  name: 'GroupPosts',
  data() {
    return {
      showMore: false,
      postFilter: 'all',
      currentPage: 1,
      postsPerPage: 9,
      authStore: useAuthStore(),
      group: {},
      postFilter: 'all',
      editDialog: false,
      addAgeGroup: false,
      dialog: false,
      selectedPost: null,
      posts: [],
      editPostData: {
        id: null,
        title: '',
        description: ''
      },
    }
  },
  computed: {
    translatedGenre() {
      return this.group?.genre ? (genreMap[this.group.genre] || this.group.genre) : 'Nav norādīts';
    },
    user() {
      return this.authStore.user
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
    // async deletePost(postId) {
    //   try {
    //     await axios.delete(`/api/posts/${postId}`, { withCredentials: true })
    //     this.posts = this.posts.filter(post => post.id !== postId)
    //     alert('Ieraksts veiksmīgi dzēsts')
    //   } catch (err) {
    //     console.error('Kļūda dzēšot ierakstu:', err)
    //     alert('Neizdevās dzēst ierakstu')
    //   }
    // },
    // openEditDialog(post) {
    //   this.editPostData = {
    //     id: post.id,
    //     title: post.title,
    //     description: post.description,
    //     private: post.private,                  
    //     dance_group_member_id: post.dance_group_member_id,
    //     picture: post.picture
    //   }
    //   this.editDialog = true
    // },
    //   async submitEdit() {
    //     try {
    //       const payload = {
    //         title: this.editPostData.title,
    //         description: this.editPostData.description,
    //         private: this.editPostData.private === 'Privāts',
    //       }

    //       if (this.editPostData.picture instanceof File) {
    //         payload.picture = this.editPostData.picture
    //       }

    //       const res = await axios.put(`/api/posts/${this.editPostData.id}`, payload, { withCredentials: true })

    //       const index = this.posts.findIndex(p => p.id === this.editPostData.id)
    //       if (index !== -1) this.posts[index] = res.data

    //       this.editDialog = false
    //       alert('Ieraksts veiksmīgi atjaunināts')
    //       this.fetchPosts() 
    //     } catch (err) {
    //       console.error('Kļūda atjauninot ierakstu:', err.response?.data || err)
    //       alert('Neizdevās atjaunināt ierakstu')
    //     }
    //   },
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
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    }
  },
  async mounted() {
    await this.authStore.fetchProfile()
    await this.fetchPosts()
    await this.fetchGroup()
  },
  watch: {
    'route.params.id': function () {
      this.fetchPosts()
    }
  }
}
</script>
