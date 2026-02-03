import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/pages/Home.vue';
import Login from '../components/pages/Login.vue';
import Registration from '../components/pages/Registration.vue';
import Profile from '../components/pages/Profile.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    {path: '/register', component: Registration },
    { path: '/profile',component: Profile, meta: { requiresAuth: true }},
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;