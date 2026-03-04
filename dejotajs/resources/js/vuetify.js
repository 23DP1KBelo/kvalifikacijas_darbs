// vuetify.js
import { createVuetify } from 'vuetify'
import { VFileUpload } from 'vuetify/labs/VFileUpload'

export const vuetify = createVuetify({
  components: {
    VFileUpload,
  },
  theme: {
    defaultTheme: 'light', // sākotnējā tēma
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#0A1931',    // tumši zila
          secondary: '#1A3D63',  // vidēji tumši zila
          accent: '#4A7FA7',     // akcenta zila
          softblue: '#B3CFE5',   // maiga zila
          background: '#F6FAFD', // gaisma fona krāsa
          surface: '#ffffff',     // kartīšu/fona krāsa
          text: '#0A1931',        // galvenā teksta krāsa
        },
      },
      dark: {
        dark: true,
        colors: {
          primary: '#0A1931',    // tumši zila joprojām
          secondary: '#1A3D63',
          accent: '#4A7FA7',
          softblue: '#4A7FA7',   // tumšākā versija fona
          background: '#1d2d44', // tumšs fona tonis
          surface: '#1B263B',     // kartīšu fons tumšajā režīmā
          text: '#f2f3f7',        // gaišs teksts tumšajā režīmā
        },
      },
    },
  },
})
