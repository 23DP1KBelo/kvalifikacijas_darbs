<template>
  <v-dialog
    v-model="dialog"
    max-width="520"
  >
    <v-card>
      <div class="d-flex flex-row justify-center align-center">
        <h2 class=" text-center text-accent mt-5">
          Rediģēt kolektīvu
        </h2>
        <v-btn
            v-if="isLeader"
            variant="text"
            class="mt-6 ml-2"
            @click="deleteGroupDialog = true"
          >
            <v-icon>mdi-trash-can-outline</v-icon>
          </v-btn>
      </div>

      <v-card-text>
        <v-form ref="editForm">
          <v-text-field
            v-model="formData.name"
            label="Nosaukums"
            required
          />

          <v-textarea
            v-model="formData.description"
            label="Apraksts"
            rows="3"
            maxlength="500"
            counter
            required
          />

          <v-text-field
            v-model="formData.city"
            label="Pilsēta"
            required
          />

          <v-text-field
            v-model="formData.address"
            label="Adrese"
            required
          />
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer />

        <v-btn
          class="text-small letter-spacing-0"
          variant="text"
          @click="close"
        >
          Atcelt
        </v-btn>

        <v-btn
          class="text-small letter-spacing-0"
          color="accent"
          @click="save"
        >
          Saglabāt
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <v-dialog
    v-model="deleteGroupDialog"
    max-width="500"
  >
    <v-card class="pa-4">
      <v-card-title class="text-h6 text-center">
        {{ group.name }}
      </v-card-title>

      <v-card-text class="text-center text-wrap text-para">
        Vai vēlies dzēst šo kolektīvu?
      </v-card-text>

      <v-card-actions class="justify-center">
        <v-btn
          variant="outlined"
          @click="deleteGroup()"
        >
          Jā
        </v-btn>

        <v-btn
          class="bg-accent"
          @click="deleteGroupDialog = false"
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

export default {
  name: 'EditDanceGroupDialog',

  props: {
    modelValue: {
      type: Boolean,
      default: false
    },

    group: {
      type: Object,
      required: true
    }
  },

  emits: [ 'update:modelValue','updated','deleted'],

  data() {
    return {
      formData: {
        name: '',
        description: '',
        city: '',
        address: ''
      },
      authStore: useAuthStore(),
      deleteGroupDialog: false,
    }
  },

  computed: {
    dialog: {
        get() {
          return this.modelValue
        },

        set(value) {
          this.$emit('update:modelValue', value)
        }
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
  },
  async mounted() {
    await this.authStore.fetchProfile()
  },
  watch: {
    modelValue(value) {
      if (value) {
        this.formData = {
          name: this.group.name || '',
          description: this.group.description || '',
          city: this.group.city || '',
          address: this.group.address || ''
        }
      }
    }
  },

  methods: {
     async deleteGroup() {
      try {
          await axios.delete(
            `/api/danceGroups/${this.group.id}`,
            {
              withCredentials: true
            }
          )

          this.deleteGroupDialog = false
          this.dialog = false

          this.$emit('deleted')

        } catch (err) {
          console.error(
            'Kļūda dzēšot kolektīvu:',
            err.response?.data || err
          )

          alert('Neizdevās dzēst kolektīvu')
        }
    },
    close() {
      this.dialog = false
    },

    async save() {
      const { valid } = await this.$refs.editForm.validate()

      if (!valid) {
        return
      }

      try {
        const res = await axios.put(
          `/api/danceGroups/${this.group.id}`,
          this.formData,
          {
            withCredentials: true
          }
        )

        this.$emit('updated', res.data)

        this.dialog = false
      } catch (err) {
        console.error(
          'Kļūda atjauninot kolektīvu:',
          err.response?.data || err
        )

        alert('Neizdevās atjaunināt kolektīvu')
      }
    }
  }
}
</script>