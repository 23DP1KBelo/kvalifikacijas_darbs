import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { vuetify } from './vuetify';
import '../css/app.css'
import axios from 'axios';


axios.defaults.withCredentials = true;
axios.defaults.baseURL = import.meta.env.VITE_API_URL;


createApp(App)
    .use(router)
    .use(vuetify)
    .mount('#app');
