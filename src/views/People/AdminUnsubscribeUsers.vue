<template>
    <div class="unsubscribe-view">
      <h2>Unsubscribe Users</h2>
      
      <form @submit.prevent="unsubscribeUsers">
        <label for="emails">Enter Email Addresses (one per line):</label>
        <textarea id="emails" v-model="emailList" rows="10" placeholder="user1@example.com&#10;user2@example.com"></textarea>
        
        <button type="submit" :disabled="loading">Unsubscribe</button>
      </form>
      
      <div v-if="message" :class="{'success-message': success, 'error-message': !success}">
        {{ message }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import axiosService from '@/services/AxiosService' // Make sure AxiosService is properly configured to handle your requests.
  
  const emailList = ref('')
  const message = ref('')
  const success = ref(false)
  const loading = ref(false)
  
  const unsubscribeUsers = async () => {
    loading.value = true
    message.value = ''
    success.value = false
  
    const emails = emailList.value.split(/\r?\n/).filter(email => email.trim() !== '') // Split email addresses by new line and remove empty ones
    
    try {
      console.log(emails)
      const response = await axiosService.post('/admin/users/unsubscribe', { emails })
      console.log(response)
      if (response.data.success) {
        message.value = 'Users successfully unsubscribed!'
        success.value = true
      } else {
        message.value = 'Failed to unsubscribe users.'
      }
    } catch (error) {
      message.value = 'An error occurred while unsubscribing users.'
    } finally {
      loading.value = false
    }
  }
  </script>
  
  <style scoped>
  .unsubscribe-view {
    width: 50%;
    margin: 0 auto;
  }
  
  textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
  }
  
  button {
    padding: 10px 15px;
    background-color: #009da5;
    color: white;
    border: none;
    cursor: pointer;
  }
  
  button:disabled {
    background-color: #ccc;
  }
  
  .success-message {
    color: green;
  }
  
  .error-message {
    color: red;
  }
  </style>
  