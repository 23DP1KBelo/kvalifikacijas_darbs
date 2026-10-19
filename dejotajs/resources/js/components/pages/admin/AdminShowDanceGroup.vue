
<template>
  <v-dialog
    :model-value="modelValue"
    max-width="1100"
    scrollable
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <v-card>
      <v-card-title class="d-flex text-wrap pa-5">
        <div>
          <h3 class="text-subhed">
            Kolektīva informācija un dokumenti
          </h3>
          <p class="text-small font-weight-bold">
            {{ activeGroup?.name || 'Kolektīva informācija' }}
          </p>
        </div>

        <v-spacer />

        <v-btn
          icon="mdi-close"
          variant="text"
          aria-label="Aizvērt"
          @click="$emit('update:modelValue', false)"
        />
      </v-card-title>

      <v-divider />

      <v-card-text class="pa-5">
        <v-alert
          v-if="error"
          type="error"
          variant="tonal"
          class="mb-5"
        >
          {{ error }}
        </v-alert>

        <v-row>
          <!-- Kolektīva informācija -->
          <v-col cols="12" md="6">
            <h3 class=" text-accent">
              Informācija par kolektīvu
            </h3>
            <v-divider/>
            <h4 class="pa-5">
              <h2 class="mb-5">
                {{ activeGroup?.name || 'Nav norādīts' }}
              </h2>

              <div class="mb-5 d-flex flex-row ga-3">
                <p class="text-subhed mb-1">Žanrs:</p>
                <v-chip color="accent">
                  {{ translatedGenre || 'Nav norādīts' }}
                </v-chip>
              </div>

                <div class="d-flex align-start mb-5">
                    <v-icon class="mr-3" color="accent">
                    mdi-map-marker-outline
                    </v-icon>
                    <span>
                    {{ activeGroup?.city || 'Pilsēta nav norādīta' }}
                    <template v-if="activeGroup?.address">
                        , {{ activeGroup.address }}
                    </template>
                    </span>
                </div>

                <h3 class="mb-2">Apraksts</h3>
                <p class="text-subhed">
                    {{
                    activeGroup?.description ||
                    'Apraksts nav pievienots.'
                    }}
                </p>

              <v-divider/>

              <div
                v-for="(leader, index) in activeGroup?.leaders || []"
                :key="leader.id ?? index"
                class="mb-5 mt-5"
              >
                <h3 class="mb-1">Vadītāji</h3>
                <p class="text-subhed">
                  {{ leader.user?.name || 'Nav norādīti' }}
                  {{ leader.user?.surname || ''}}
                </p>
              </div>

              <v-divider class="my-5" />

            </h4>
          </v-col>

          <!-- Laba puse -->
          <v-col cols="12" md="6">
            <h3 class="ma-5 d-flex align-center">
                <v-icon class="mr-3" color="accent">
                    mdi-image-outline
                </v-icon>
                Attēls  
            </h3>
            <v-divider/>
            <v-card-text>
                <v-img
                    v-if="activeGroup?.picture_url"
                    :src="activeGroup.picture_url"
                    height="280"
                    cover
                    alt="Kolektīva attēls"
                />
                <div
                    v-else
                    class="d-flex flex-column align-center justify-center pa-10"
                >
                    <v-icon size="48" color="grey">
                    mdi-image-off-outline
                    </v-icon>
                    <p class="mt-3">Attēls nav pievienots.</p>
                </div>
                </v-card-text>

            <h3 class="ma-5 d-flex align-center">
                <v-icon class="mr-3" color="accent">
                    mdi-file-document-outline
                </v-icon>
                Apliecinājums  
            </h3>

            <v-divider />

            <v-card-text class="pa-5">
              <div class="d-flex align-center mb-4">
                <v-icon size="36" color="primary" class="mr-3">
                  {{ fileIcon }}
                </v-icon>

                <div class="flex-grow-1">
                  <div class="font-weight-medium">
                    {{ fileName }}
                  </div>
                  <div class="text-caption text-medium-emphasis">
                    Pievienotais kolektīva dokuments
                  </div>
                </div>
              </div>

              <iframe
                v-if="fileType === 'pdf' && documentUrl"
                :src="documentUrl"
                title="Dokumenta priekšskatījums"
                width="100%"
                height="400"
                style="border: 1px solid #ddd; border-radius: 8px;"
              />

              <v-img
                v-else-if="fileType === 'image' && documentUrl"
                :src="documentUrl"
                max-height="400"
                contain
                rounded="lg"
              />

              <div
                v-else
                class="text-center pa-6 bg-grey-lighten-4 rounded-lg"
              >
                <v-icon size="48" color="grey-darken-1">
                  mdi-file-download-outline
                </v-icon>
                <p class="mt-3 text-body-2">
                  {{
                    fileType === 'word'
                      ? 'Word dokumentu var atvērt vai lejupielādēt.'
                      : 'Dokumenta priekšskatījums nav pieejams.'
                  }}
                </p>
              </div>

              <v-btn
                block
                color="primary"
                variant="tonal"
                prepend-icon="mdi-open-in-new"
                class="mt-4"
                :href="documentUrl || undefined"
                target="_blank"
                rel="noopener noreferrer"
                :disabled="!documentUrl"
              >
                Atvērt dokumentu
              </v-btn>

              <v-btn
                block
                color="primary"
                variant="outlined"
                prepend-icon="mdi-download"
                class="mt-3"
                :href="documentUrl || undefined"
                :download="fileName"
                :disabled="!documentUrl"
              >
                Lejupielādēt failu
              </v-btn>
            </v-card-text>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios'


const genreMap = {
  'lyrical dance': 'Liriskā deja',
  'contemporary dance': 'Mūsdienīgās dejas',
  'ballet': 'Balets',
  'hip hop': 'Hip-hops',
  'folk dance': 'Tautas dejas',
  'other': 'Cits'
};


export default {
  name: 'AdminDanceGroupView',

  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    group: {
      type: Object,
      default: null
    },
    groupId: {
      type: [Number, String],
      default: null
    }
  },

  emits: ['update:modelValue'],

  data() {
    return {
      fetchedGroup: null,
      loading: false,
      error: '',
      requestId: 0
    }
  },

  computed: {
    activeGroup() {
      return this.fetchedGroup || this.group
    },

    translatedGenre() {
      return this.group?.genre ? (genreMap[this.group.genre] || this.group.genre) : 'Nav norādīts';
    },

    fileName() {
    const approval = this.activeGroup?.approval

    if (!approval) return 'Dokuments nav pievienots'

    const path =
        typeof approval === 'string'
        ? approval
        : approval.original_name ||
            approval.file_name ||
            approval.path ||
            approval.name ||
            ''

    if (!path) return 'Dokuments nav pievienots'

    const cleanPath = String(path)
        .split('?')[0]
        .split('#')[0]

    const name = cleanPath.split('/').pop()

    let extension = ''

    const dotIndex = name.lastIndexOf('.')
    if (dotIndex !== -1) {
        extension = name.substring(dotIndex).toLowerCase()
    }

    const allowedExtensions = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png']

    if (!allowedExtensions.includes(extension)) {
        extension = ''
    }

    return `Kolektīva apstiprinājuma dokuments (${this.group.name})${extension}`
    },

    fileType() {
      const name = this.fileName.toLowerCase()

      if (/\.pdf$/i.test(name)) return 'pdf'
      if (/\.docx?$/i.test(name)) return 'word'

      if (/\.(jpg|jpeg|png|gif|webp|bmp|avif)$/i.test(name)) {
        return 'image'
      }

      const mime = String(
        this.activeGroup?.approval_mime_type ||
        this.activeGroup?.approval_mime ||
        ''
      ).toLowerCase()

      if (mime === 'application/pdf') return 'pdf'

      if (
        mime === 'application/msword' ||
        mime ===
          'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
      ) {
        return 'word'
      }

      if (mime.startsWith('image/')) return 'image'

      return 'other'
    },

    fileIcon() {
      if (this.fileType === 'pdf') return 'mdi-file-pdf-box'
      if (this.fileType === 'word') return 'mdi-file-word-box'
      if (this.fileType === 'image') return 'mdi-file-image-outline'

      return 'mdi-file-document-outline'
    },

    documentUrl() {
      const approval = this.activeGroup?.approval

      if (!approval) return ''

      if (typeof approval === 'object') {
        const url =
          approval.url ||
          approval.download_url ||
          approval.path ||
          ''

        if (!url) return ''

        if (/^https?:\/\//i.test(url)) return url

        return `${window.location.origin}/${url.replace(/^\/+/, '')}`
      }

      if (/^https?:\/\//i.test(approval)) {
        return approval
      }

      return `${window.location.origin}/${approval.replace(/^\/+/, '')}`
    }
  },

  watch: {
    modelValue(isOpen) {
      if (isOpen) {
        this.fetchGroup()
      } else {
        this.requestId++
        this.loading = false
      }
    },

    groupId(newId, oldId) {
      if (this.modelValue && newId !== oldId) {
        this.fetchGroup()
      }
    }
  },

  methods: {
    async fetchGroup() {
      const id = this.groupId ?? this.group?.id

      if (id == null || id === '') {
        this.fetchedGroup = null
        this.error = 'Nav norādīts kolektīva ID.'
        this.loading = false
        return
      }

      const currentRequest = ++this.requestId

      this.loading = true
      this.error = ''

      try {
        const response = await axios.get(
          `/api/admin/danceGroup/${encodeURIComponent(id)}`,
          {
            withCredentials: true,
            headers: {
              Accept: 'application/json'
            }
          }
        )

        if (currentRequest !== this.requestId) return

        const result = response.data?.data ?? response.data
        const groupData = result?.group ?? result

        if (!groupData || groupData.id == null) {
          this.fetchedGroup = null
          this.error = 'Kolektīvs nav atrasts.'
          return
        }

        this.fetchedGroup = groupData
      } catch (error) {
        if (currentRequest !== this.requestId) return

        console.error(
          'Neizdevās ielādēt kolektīvu:',
          error.response?.data ?? error
        )

        this.fetchedGroup = null

        if (error.response?.status === 404) {
          this.error = 'Kolektīvs nav atrasts.'
        } else if (error.response?.status === 401) {
          this.error = 'Nepieciešams autorizēties.'
        } else if (error.response?.status === 403) {
          this.error = 'Nav tiesību skatīt šo kolektīvu.'
        } else {
          this.error =
            error.response?.data?.message ||
            'Neizdevās ielādēt kolektīva informāciju.'
        }
      } finally {
        if (currentRequest === this.requestId) {
          this.loading = false
        }
      }
    }
  }
}
</script>
