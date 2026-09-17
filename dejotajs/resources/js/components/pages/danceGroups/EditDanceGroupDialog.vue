<template>
  <v-dialog
    v-model="dialog"
    max-width="520"
  >
    <v-card>
      <v-card-title class="text-h6 text-center text-accent">
        Rediģēt kolektīvu
      </v-card-title>

      <v-card-text>
        <v-form ref="editForm">
          <v-text-field
            v-model="formData.name"
            label="Nosaukums"
            :rules="nameRules"
            required
          />

          <v-textarea
            v-model="formData.description"
            label="Apraksts"
            rows="3"
            :rules="descriptionRules"
            maxlength="500"
            counter
            required
          />

          <v-text-field
            v-model="formData.city"
            label="Pilsēta"
            :rules="cityRules"
            required
          />

          <v-text-field
            v-model="formData.address"
            label="Adrese"
            :rules="addressRules"
            required
          />
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer />

        <v-btn
          variant="text"
          @click="close"
        >
          Atcelt
        </v-btn>

        <v-btn
          color="accent"
          @click="save"
        >
          Saglabāt
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import axios from 'axios'

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

  emits: ['update:modelValue', 'updated'],

  data() {
    return {
      formData: {
        name: '',
        description: '',
        city: '',
        address: ''
      },
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
    }
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