import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/AuthStore'; // Import your Pinia store
import Dashboard from '@/views/Dashboard.vue'; // Adjust the path as necessary
import EmailSendDirect from '@/views/Emails/EmailSendDirect.vue'; 
import EmailQueGroup from '@/views/Emails/EmailQueGroup.vue';
import EmailSeriesEditor from '@/views/Emails/EmailSeriesEditor.vue'; // Adjust the path as necessary
import EmailSeriesTitles from '@/views/Emails/EmailSeriesTitles.vue';
import LoginUser from '@/views/People/LoginUser.vue';
import UnsubscribeUser from '@/views/People/UnsubscribeUser.vue';
import UserChangeEmail from '@/views/People/UserChangeEmail.vue';



const routes = [
  
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
  },
  {
    path: '/email/series/titles',
    name: 'EmailSeriesTitles',
    component: EmailSeriesTitles,
  },
  {
    path: '/email/series/:series?/:sequence?',
    name: 'EmailSeriesEditor',
    component: EmailSeriesEditor,
  },
  
  {
    path: '/email/direct',
    name: 'EmailSendDirect',
    component: EmailSendDirect,
  },
  {
    path: '/email/group/que',
    name: 'EmailQueGroup',
    component: EmailQueGroup,
  },
  {
    path: '/',
    name: 'LoginUser',
    component: LoginUser,
  },
  {
    path: '/email/unsubscribe/:cid/:hash',
    name: 'UnsubscribeUser',
    component: UnsubscribeUser,
  },
  {
    path: '/email/user/update/:cid/:hash',
    name: 'UserChangeEmail',
    component: UserChangeEmail,
  },
  
  // Add more routes here
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore(); // Access the Pinia store

  // Check if the route is the login page, if so, allow access
  if (to.name === 'LoginUser') {
    next();
  } else {
    // If not, check if the user is authenticated
    if (!authStore.isAuthenticated) {
      next({ name: 'LoginUser' });
    } else {
      next();
    }
  }
});

export default router;
