// vuetify.js
import { createVuetify } from 'vuetify'
import { VFileUpload } from 'vuetify/labs/VFileUpload'

export const vuetify = createVuetify({
  components: {
    VFileUpload,
  },
  theme: {
    defaultTheme: 'light', 
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#0A1931',    
          secondary: '#1A3D63',  
          accent: '#4A7FA7',     
          softblue: '#B3CFE5',   
          background: '#F6FAFD', 
          surface: '#ffffff',     
          text: '#0A1931',        
        },
      },
      dark: {
        dark: true,
        colors: {
          primary: '#7CC6FE',      
          secondary: '#3A86C8',    
          accent: '#A5D8FF',
          background: '#07111F',   
          surface: '#0F1B2D',      
          softblue: '#16263D',
          text: '#F4F9FF',         
        },
      },
    },
  },
})
