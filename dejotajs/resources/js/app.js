import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { vuetify } from './vuetify';
import '../css/app.css'
import axios from 'axios';


axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://127.0.0.1:8000';


createApp(App)
    .use(router)
    .use(vuetify)
    .mount('#app');
