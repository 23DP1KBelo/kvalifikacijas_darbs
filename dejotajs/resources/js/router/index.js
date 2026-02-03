import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/pages/Home.vue';
import Login from '../components/pages/Login.vue';
import Registration from '../components/pages/Registration.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    {path: '/register', component: Registration }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;