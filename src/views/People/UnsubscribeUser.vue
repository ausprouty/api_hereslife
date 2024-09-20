<template>
    <div class="unsubscribe-container">
      <h2>Are you sure you want to unsubscribe?</h2>
      <p>If you unsubscribe, you will no longer receive our newsletters.</p>
  
      <button @click="unsubscribe" class="unsubscribe-button" :disabled="loading">
        {{ loading ? 'Processing...' : 'Unsubscribe' }}
      </button>
  
      <div v-if="message" :class="['message', success ? 'success' : 'error']">
        {{ message }}
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import { useRoute, useRouter } from 'vue-router'; // Assuming you're using vue-router
  import AxiosService from '@/services/AxiosService'; // Adjust the path according to your project structure
  
  export default {
    setup() {
      const route = useRoute();
      const router = useRouter();
      const loading = ref(false);
      const message = ref('');
      const success = ref(false);
  
      // Get CID and HASH from the router params
      const CID = route.params.CID;
      const HASH = route.params.HASH;
  
      const unsubscribe = async () => {
        loading.value = true;
        message.value = '';
  
        try {
          const postData = { CID, HASH };
          const response = await AxiosService.post('/user/unsubscribe', postData);
  
          if (response.data.success) {
            success.value = true;
            message.value = 'You have successfully unsubscribed.';
            // Optionally, redirect or perform any further actions here
          } else {
            throw new Error(response.data.message || 'Unsubscription failed.');
          }
        } catch (error) {
          success.value = false;
          message.value = error.message || 'An error occurred. Please try again.';
        } finally {
          loading.value = false;
        }
      };
  
      return {
        unsubscribe,
        loading,
        message,
        success,
      };
    },
  };
  </script>
  
  <style scoped>
  .unsubscribe-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
  }
  
  .unsubscribe-button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #f38b3c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
  }
  
  .unsubscribe-button:disabled {
    background-color: #ccc;
  }
  
  .message {
    margin-top: 20px;
    font-size: 16px;
  }
  
  .success {
    color: #65c058;
  }
  
  .error {
    color: #ff0000;
  }
  </style>
  