<template>
    <div class="update-email-container">
      <h2>Update Your Email Address</h2>
      <p>Please enter your new email address below:</p>
  
      <input
        type="email"
        v-model="newEmail"
        placeholder="Enter new email address"
        class="email-input"
        :disabled="loading"
      />
  
      <button @click="updateEmail" class="update-button" :disabled="loading || !newEmail">
        {{ loading ? 'Processing...' : 'Update Email' }}
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
      const newEmail = ref('');
      const message = ref('');
      const success = ref(false);
  
      // Get CID and HASH from the router params
      const cid = route.params.cid;
      const hash = route.params.hash;
  
      const updateEmail = async () => {
        if (!validateEmail(newEmail.value)) {
          message.value = 'Please enter a valid email address.';
          return;
        }
  
        loading.value = true;
        message.value = '';
  
        try {
          const postData = { 
            cid, 
            hash, 
            email: newEmail.value 
          };
          console.log (route)
          console.log (postData)
          const response = await AxiosService.post('/user/update', postData);
          console.log (response)
          if (response.data.success) {
            success.value = true;
            message.value = 'Your email address has been updated successfully.';
            // Optionally, redirect or perform further actions here
          } else {
            throw new Error(response.data.message || 'Email update failed.');
          }
        } catch (error) {
          success.value = false;
          message.value = error.message || 'An error occurred. Please try again.';
        } finally {
          loading.value = false;
        }
      };
  
      const validateEmail = (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
      };
  
      return {
        newEmail,
        updateEmail,
        loading,
        message,
        success,
      };
    },
  };
  </script>
  
  <style scoped>
  .update-email-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
  }
  
  .email-input {
    padding: 10px;
    width: 80%;
    font-size: 16px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  
  .update-button {
    padding: 10px 20px;
    font-size: 16px;
    background-color: #f38b3c;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .update-button:disabled {
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
  