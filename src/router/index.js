import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/AuthStore'; // Import your Pinia store
import Dashboard from '@/views/Dashboard.vue'; // Adjust the path as necessary
import EmailSendDirect from '@/views/Emails/EmailSendDirect.vue'; 
import EmailQueGroup from '@/views/Emails/EmailQueGroup.vue';
import EmailSeriesEditor from '@/views/Emails/EmailSeriesEditor.vue'; // Adjust the path as necessary
import EmailSeriesTitles from '@/views/Emails/EmailSeriesTitles.vue';
import LoginUser from '@/views/People/LoginUser.vue';
import UnsubscribeUser from '@/views/People/UnsubscribeUser.vue';
import UserChangeDetails from '@/views/People/UserChangeDetails.vue';
import AdminUnsubscribeUsers from '@/views/People/AdminUnsubscribeUsers.vue';



const routes = [
  
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/email/series/titles',
    name: 'EmailSeriesTitles',
    component: EmailSeriesTitles,
    meta: { requiresAuth: true },
  },
  {
    path: '/email/series/:series?/:sequence?',
    name: 'EmailSeriesEditor',
    component: EmailSeriesEditor,
    meta: { requiresAuth: true },
  },
  
  {
    path: '/email/direct',
    name: 'EmailSendDirect',
    component: EmailSendDirect,
    meta: { requiresAuth: true },
  },
  {
    path: '/email/group/que',
    name: 'EmailQueGroup',
    component: EmailQueGroup,
    meta: { requiresAuth: true },
  },
  {
    path: '/',
    name: 'LoginUser',
    component: LoginUser,
  },
  {
    path: '/user/unsubscribe/:cid/:hash',
    name: 'UnsubscribeUser',
    component: UnsubscribeUser,
    meta: { requiresAuth: false },
  },
  {
    path: '/user/update/:cid/:hash',
    name: 'UserChangeDetails',
    component: UserChangeDetails,
    meta: { requiresAuth: false },
  },
  {
    path: '/admin/unsubscribe', // Admin page to unsubscribe users  
    name: 'AdminUnsubscribeUsers',
    component: AdminUnsubscribeUsers,
    meta: { requiresAuth: true },
  }
  
  // Add more routes here
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore(); // Access the Pinia store

  // Check if the route requires authentication
  if (to.meta.requiresAuth) {
    // If the user is not authenticated, redirect to the login page
    if (!authStore.isAuthenticated) {
      next({ name: 'LoginUser' });
    } else {
      next(); // Proceed to the route
    }
  } else {
    next(); // Proceed to the route that doesn't require authentication
  }
});


export default router;
