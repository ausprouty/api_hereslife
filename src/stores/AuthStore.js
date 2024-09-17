import { defineStore } from 'pinia';
import axiosService from '@/services/axiosService';
import router from '@/router';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
    administratorExists: false,
  }),

  getters: {
    isAuthenticated: (state) => state.user !== null && state.user !== undefined,
  },

  actions: {
    async checkIfAdministratorExists() {
      try {
        const { data } = await axiosService.get('admin/exists', { skipUserId: true });
        console.log('Admin exists:', data);
        this.administratorExists = data.data === 'TRUE';
      } catch (error) {
        console.error('Failed to check if admin exists:', error);
        this.administratorExists = false;
      }
    },

    async register(userData) {
      try {
        const { data } = await axiosService.post('admin/create', userData, { skipUserId: true });
        if (data.success === 'TRUE') {
          this.token = data.token;
          this.user = data.user;
          this.administratorExists = true;
        } else {
          alert('Administrator not created. Reprogramming required');
          this.administratorExists = false;
        }
      } catch (error) {
        console.error('Registration failed', error);
      }
    },

    async login(credentials) {
      try {
        console.log(credentials);
        const { data } = await axiosService.post('admin/login', credentials, { skipUserId: true });
        if (data.success === 'FALSE') {
          alert('Invalid username or password');
          return 'Invalid username or password';
        }
        this.token = data.token;
        this.user = data.user;
        
        router.push('/dashboard'); // Redirect after successful login
        return 'Success';
      } catch (error) {
        console.error('Login failed', error);
      }
    },

    logout() {
      this.token = null;
      this.user = null;
      router.push('/login'); // Redirect to login after logout
    },

    async checkAuth() {
      const token = this.token;
      if (token) {
        try {
          const credentials = {
            token,
            user: this.user,
          };
          const { data } = await axiosService.post('admin/checkAuth', credentials);
          if (data.success === 'FALSE') {
            this.logout();
            router.push('/login');
          } else {
            console.log('Login success:', data);
            this.token = data.token;
            this.user = data.user;
          }
        } catch (error) {
          console.error('Auth check failed', error);
          this.logout(); // Log out and redirect if check fails
        }
      } else {
        router.push('/login'); // Redirect to login if no token found
      }
    },
  },

  persist: {
    enabled: true,
    strategies: [
      {
        key: 'auth',
        storage: localStorage,  // Pinia automatically syncs with localStorage
        paths: ['token', 'user'], // Only persist token and user
      },
    ],
  },
});
