import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import Home from '../components/pages/Home.vue';
import Login from '../components/pages/Login.vue';
import Registration from '../components/pages/Registration.vue';
import Profile from '../components/pages/Profile.vue';
import AdminDashboard from '../components/pages/AdminDashboard.vue';
import DanceGroupForm from '../components/pages/danceGroups/DanceGroupForm.vue';
import DanceGroupPage from '../components/pages/danceGroups/DanceGroupPage.vue';
import NoAccess from '../components/errors/NoAccess.vue';
import DancerApproval from '../components/pages/DancerApproval.vue';
import LeaderForm from '../components/pages/LeaderForm.vue';
import DanceGroupList from '../components/pages/danceGroups/DanceGroupList.vue';
import LeaderApproval from '../components/pages/LeaderApproval.vue';
import DanceGroupDashboard from '../components/pages/danceGroups/DanceGroupDashboard.vue';
import DanceGroupInfo from '../components/pages/danceGroups/DanceGroupInfo.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Registration },
    { path: '/profile', component: Profile, meta: { requiresAuth: true } },
    { path: '/dashboard', component: AdminDashboard, meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/danceForm', component: DanceGroupForm},
    { path: '/group/:id', component: DanceGroupPage, meta: { requiresAuth: true }},
    { path: '/no-access', component: NoAccess },
    { path: '/dancerApproval/:id', component: DancerApproval, meta: { requiresAuth: true, requiresLeader: true } },
    { path: '/dance-leader', component: LeaderForm, meta: { requiresAuth: true } },
    { path: '/danceGroup-list', component: DanceGroupList, meta: {requiresAuth: true}},
    { path: '/leaderApproval/:id', component: LeaderApproval, meta: {requiresAuth: true, requiresLeader:true}},
    { path: '/dance-groups', component: DanceGroupDashboard},
    { path: '/group-info/:id', component: DanceGroupInfo},
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
