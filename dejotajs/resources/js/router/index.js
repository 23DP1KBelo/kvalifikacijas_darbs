import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import Home from '../components/pages/Home.vue';
import Login from '../components/pages/Login.vue';
import Registration from '../components/pages/Registration.vue';
import Profile from '../components/pages/Profile.vue';
import AdminDashboard from '../components/pages/AdminDashboard.vue';
import DanceGroupForm from '../components/pages/DanceGroupForm.vue';
import DanceGroupPage from '../components/pages/DanceGroupPage.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Registration },
    { path: '/profile', component: Profile, meta: { requiresAuth: true } },
    { path: '/dashboard', component: AdminDashboard, meta: { requiresAuth: true, requiresAdmin: true } },
    {path: '/danceForm', component: DanceGroupForm},
    { path: '/group', component: DanceGroupPage, meta: { requiresAuth: true }},
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Router guard
router.beforeEach(async (to, from, next) => {
    if (!to.meta.requiresAuth) return next();

    try {
        const res = await axios.get('/user', { withCredentials: true });
        const { logged_in, user } = res.data;

        if (!logged_in || !user) return next('/login');

        if (to.meta.requiresAdmin && user.role !== 'admin') return next('/');

        return next();
    } catch (e) {
        return next('/login');
    }
});


export default router;
