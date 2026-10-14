import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import Home from '../components/pages/Home.vue';
import Login from '../components/pages/Login.vue';
import Registration from '../components/pages/Registration.vue';
import Profile from '../components/pages/Profile.vue';
import AdminDashboard from '../components/pages/admin/AdminDashboard.vue';
import DanceGroupForm from '../components/pages/danceGroups/CreateDanceGroup.vue';
import DanceGroupPage from '../components/pages/danceGroups/DanceGroupPage.vue';
import NoAccess from '../components/errors/NoAccess.vue';
import LeaderForm from '../components/pages/leader/CreateLeader.vue';
import DanceGroupList from '../components/pages/leader/AddLeader.vue';
import DanceGroupDashboard from '../components/pages/danceGroups/SearchDanceGroup.vue';
import DanceGroupInfo from '../components/pages/danceGroups/ShowDanceGroup.vue';
import PostsShow from '../components/pages/posts/ShowPosts.vue';
import AdmissonDashboard from '../components/pages/admissions/ShowAdmission.vue';
import CalenderDashboard from '../components/pages/calender/CalenderDashboard.vue';
import EventForm from '../components/pages/calender/EventForm.vue';
import AdminStats from '../components/pages/admin/AdminStats.vue';
import DancerChat from '../components/pages/chat/dancerChat.vue';
import Memberpage from '../components/pages/leader/Memberpage.vue';
const routes = [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Registration },
    { path: '/profile', component: Profile, meta: { requiresAuth: true } },
    { path: '/dashboard', component: AdminDashboard, meta: { requiresAuth: true, requiresAdmin: true } },
    { path: '/create-dance-group', component: DanceGroupForm},
    { path: '/group/:id', component: DanceGroupPage},
    { path: '/no-access', component: NoAccess },
    { path: '/dance-leader', component: LeaderForm, meta: { requiresAuth: true } },
    { path: '/danceGroup-list', component: DanceGroupList, meta: {requiresAuth: true}},
    { path: '/dance-groups', component: DanceGroupDashboard},
    { path: '/group-info/:id', component: DanceGroupInfo},
    { path: '/posts', component: PostsShow },
    { path: '/admisson', component: AdmissonDashboard},
    { path: '/calender', component: CalenderDashboard},
    { path: '/calender/event', component: EventForm, meta: {requiresAuth: true, requiresLeader: true}},
    { path: '/dashboard/stats', component: AdminStats, meta: { requiresAuth: true, requiresAdmin: true }},
    { path: '/chat', component: DancerChat},
    { path: '/members/:id', component: Memberpage, meta: {requiresAuth: true, requiresLeader: true}},
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
  if (!to.meta.requiresAuth) return next();

try {
  const res = await axios.get('/api/profile', {
    withCredentials: true
  })

  const user = res.data.user || res.data
  const members = res.data.dance_group_members || []

  // Lietotājs nav nepieciešams publiskām lapām
  if (to.meta.requiresAuth && !user) {
    return next('/login')
  }

  // Admin pārbaude
  if (to.meta.requiresAdmin) {
    if (!user || user.role !== 'admin') {
      return next('/no-access')
    }
  }

  // Vadītāja pārbaude
  if (to.meta.requiresLeader) {
      if (!user) {
        return next('/login')
      }

      const isLeader = members.some(member =>
        member.role === 'leader' &&
        member.status === 'approved'
      )

      if (!isLeader) {
        return next('/no-access')
      }
    }

    next()

  } catch (error) {
    if (to.meta.requiresAuth || to.meta.requiresLeader || to.meta.requiresAdmin) {
      return next('/login')
    }

    next()
  }
});


export default router;
