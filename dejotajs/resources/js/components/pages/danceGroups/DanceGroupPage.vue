<template>
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
</template>

<script>
import axios from 'axios'
import { useRoute } from 'vue-router'

export default {
  name: 'GroupPosts',
  data() {
    return {
      group: {},
      posts: [],
      editDialog: false,
      user: null,
      editPostData: {
        id: null,
        title: '',
        description: ''
      }
    }
  },
  setup() {
    const route = useRoute()
    return { route }
  },
  computed: {
    isLeader() {
      return this.group?.leaders?.some(
        leader => Number(leader.user?.id) === Number(this.user?.id)
      ) ?? false
    }
  },
  methods: {
    async fetchPosts() {
      try {
        const groupId = this.$route.params.id
        const res = await axios.get(`/api/my-posts/${groupId}`, { withCredentials: true })
        this.group = res.data.dance_group
        this.posts = res.data.posts
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
        alert('Ieraksts veiksmīgi dzēsts')
      } catch (err) {
        console.error('Kļūda dzēšot ierakstu:', err)
        alert('Neizdevās dzēst ierakstu')
      }
    },
    async fetchUser() {
      try {
        const res = await axios.get('/api/profile', {
          withCredentials: true
        })

        this.user = res.data.user
      } catch (err) {
        this.user = null
      }
    },
    openEditDialog(post) {
      this.editPostData = {
        id: post.id,
        title: post.title,
        description: post.description,
        private: post.private,                  
        dance_group_member_id: post.dance_group_member_id,
        picture: post.picture
      }
      this.editDialog = true
    },
      async submitEdit() {
        try {
          const payload = {
            title: this.editPostData.title,
            description: this.editPostData.description,
            private: this.editPostData.private === 'Privāts',
          }

          if (this.editPostData.picture instanceof File) {
            payload.picture = this.editPostData.picture
          }

          const res = await axios.put(`/api/posts/${this.editPostData.id}`, payload, { withCredentials: true })

          const index = this.posts.findIndex(p => p.id === this.editPostData.id)
          if (index !== -1) this.posts[index] = res.data

          this.editDialog = false
          alert('Ieraksts veiksmīgi atjaunināts')
          this.fetchPosts() 
        } catch (err) {
          console.error('Kļūda atjauninot ierakstu:', err.response?.data || err)
          alert('Neizdevās atjaunināt ierakstu')
        }
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
    formatDate(date) {
      return new Date(date).toLocaleDateString()
    }
  },
  async mounted() {
    await this.fetchUser()
    await this.fetchPosts()
  },
  watch: {
    'route.params.id': function () {
      this.fetchPosts()
    }
  }
}
</script>

<style scoped>
.group-page {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(74, 144, 226, 0.12), transparent 32%),
    linear-gradient(180deg, #f7f9fc 0%, #ffffff 45%);
}

.hero-section {
  padding: 48px 0 24px;
}

.hero-card {
  background: rgba(255, 255, 255, 0.86);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(120, 144, 156, 0.18);
  border-radius: 28px;
  padding: 32px;
  box-shadow: 0 20px 60px rgba(31, 41, 55, 0.08);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.posts-container {
  padding-bottom: 64px;
}

.post-card {
  border-radius: 28px;
  overflow: hidden;
  background: #ffffff;
  border: 1px solid rgba(120, 144, 156, 0.16);
  box-shadow: 0 18px 45px rgba(31, 41, 55, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.post-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 26px 70px rgba(31, 41, 55, 0.12);
}

.post-image-wrapper {
  position: relative;
}

.post-image {
  border-bottom: 1px solid rgba(120, 144, 156, 0.16);
}

.post-actions {
  position: absolute;
  top: 16px;
  right: 16px;
  display: flex;
  gap: 10px;
}

.action-btn {
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(8px);
}

.post-description {
  line-height: 1.75;
  white-space: pre-line;
}

.empty-card {
  border-radius: 28px;
  background: #ffffff;
  border: 1px dashed rgba(120, 144, 156, 0.35);
  box-shadow: 0 18px 45px rgba(31, 41, 55, 0.06);
}

.dialog-card {
  border-radius: 24px !important;
}

.cursor-pointer {
  cursor: pointer;
}

@media (max-width: 700px) {
  .hero-card {
    flex-direction: column;
    align-items: flex-start;
    padding: 24px;
  }

  .hero-actions {
    width: 100%;
    justify-content: space-between;
  }

  .post-image {
    height: 260px !important;
  }
}
</style>

