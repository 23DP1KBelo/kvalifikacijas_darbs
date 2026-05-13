import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import Home from '../components/pages/Home.vue';
import Login from '../components/pages/Login.vue';
import Registration from '../components/pages/Registration.vue';
import Profile from '../components/pages/Profile.vue';
import AdminDashboard from '../components/pages/admin/AdminDashboard.vue';
import DanceGroupForm from '../components/pages/danceGroups/DanceGroupForm.vue';
import DanceGroupPage from '../components/pages/danceGroups/DanceGroupPage.vue';
import NoAccess from '../components/errors/NoAccess.vue';
import DancerApproval from '../components/pages/DancerApproval.vue';
import LeaderForm from '../components/pages/LeaderForm.vue';
import DanceGroupList from '../components/pages/danceGroups/DanceGroupList.vue';
import LeaderApproval from '../components/pages/LeaderApproval.vue';
import DanceGroupDashboard from '../components/pages/danceGroups/DanceGroupDashboard.vue';
import DanceGroupInfo from '../components/pages/danceGroups/DanceGroupInfo.vue';
import PostCreate from '../components/pages/PostCreate.vue';
import PostsShow from '../components/pages/PostsShow.vue';
import AgeGroupForm from '../components/pages/AgeGroupForm.vue';
import DanceGroupProfile from '../components/pages/danceGroups/DanceGroupProfile.vue';
import AdmissonForm from '../components/pages/AdmissonForm.vue';
import AdmissonDashboard from '../components/pages/AdmissonDashboard.vue';
import CalenderDashboard from '../components/pages/calendar/CalenderDashboard.vue';
import EventForm from '../components/pages/calendar/EventForm.vue';
import AdminStats from '../components/pages/admin/AdminStats.vue';

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
    { path: '/create-post/:id', component: PostCreate, meta: { requiresAuth: true, requiresLeader: true }},
    { path: '/posts', component: PostsShow },
    { path: '/age-group-form/:id', component: AgeGroupForm, meta: { requiresAuth: true, requiresLeader: true }},
    { path: '/group-profile/:id', component: DanceGroupProfile, meta: { requiresAuth: true, requiresLeader: true }},
    { path: '/admisson/:id', component: AdmissonForm, meta: { requiresAuth: true, requiresLeader: true }},
    { path: '/admisson', component: AdmissonDashboard},
    { path: '/calender', component: CalenderDashboard},
    { path: '/calender/event', component: EventForm, meta: {requiresAuth: true, requiresLeader: true}},
    { path: '/dashboard/stats', component: AdminStats, meta: { requiresAuth: true, requiresAdmin: true }},
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// router.beforeEach(async (to, from, next) => {
//   if (!to.meta.requiresAuth) return next();

//   try {
//     const res = await axios.get('/api/profile', {
//       withCredentials: true
//     });

//     const user = res.data.user || res.data;
//     const members = res.data.dance_group_members || [];

//     if (!user) return next('/login');

//     if (to.meta.requiresAdmin && user.role !== 'admin') {
//       return next('/no-access');
//     }

//     if (to.meta.requiresLeader) {
//       const isLeader = members.some(member =>
//         member.role === 'leader' &&
//         member.status === 'approved'
//       );

//       if (!isLeader) {
//         return next('/no-access');
//       }
//     }

//     return next();

//   } catch (e) {
//     return next('/login');
//   }
// });


export default router;
