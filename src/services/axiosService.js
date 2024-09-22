import axios from 'axios';
import { useAuthStore } from '@/stores/AuthStore'; // Import the auth store

const apiUrl = import.meta.env.VITE_APP_API_URL;

const axiosInstance = axios.create({
  baseURL: apiUrl,
});

axiosInstance.interceptors.request.use(config => {
  const authStore = useAuthStore();  // Access the Pinia store

  const siteToken = import.meta.env.VITE_APP_HL_API_KEY;
  const userToken = authStore.token; // Get token from Pinia store
  const userId = authStore.user ? authStore.user.id : null; // Get user ID from Pinia store
  
  if (siteToken) {

    config.headers['Authorization'] = `Bearer ${siteToken}`;
  }
  
  if (userToken) {

    config.headers['User-Authorization'] = `Bearer ${userToken}`;
  }
  // Append userId as a query parameter `u` if:
  // 1. The userId exists.
  // 2. The request config does not have a `skipUserId` flag set to true.
  if (userId && !config.skipUserId) {
    config.params = config.params || {};
    config.apiKey = siteToken;
    config.params['u'] = userId;
  }

  //console.log('Modified Axios Request Config:', config);
  return config;
}, error => {
  return Promise.reject(error);
});

export default axiosInstance;
