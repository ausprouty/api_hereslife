<template>
    <div>
      <label for="email-series">Select an Email Series:</label>
      <select v-model="selectedSeries" @change="fetchSeriesData">
        <option v-for="series in seriesOptions" :key="series.name" :value="series.name">
          {{ series.public_name }}
        </option>
      </select>
  
      <table v-if="emailSeries.length">
        <thead>
          <tr>
            <th>Sequence</th>
            <th>Subject Line</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(email, index) in emailSeries" :key="index" @click="goToEmailDetails(email.series_sequence, selectedSeries)">
            <td>{{ email.series_sequence }}</td>
            <td>{{ email.subject }}</td>
          </tr>
        </tbody>
      </table>
  
      <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import axiosService from '@/services/AxiosService'; // assuming you have a service file
  
  export default {
    name: 'EmailSeriesTitles',
    setup() {
      const selectedSeries = ref('');
      const emailSeries = ref([]);
      const errorMessage = ref('');
      
      const seriesOptions = [
      
        { name: 'DBSstart', public_name: 'DBS Start' },
        { name: 'DBSmultiply', public_name: 'DBS Multiply' },
        { name: 'DBSlifeprinciples', public_name: 'DBS Life Principles' },
        { name: 'DBSleadership', public_name: 'DBS Leadership' },
        { name: 'Followup', public_name: 'Followup' },
        { name: 'MyFriends', public_name: 'MyFriends' },
        { name: 'Tracts', public_name: 'Tracts' }
      ];
  
      const fetchSeriesData = async () => {
        if (!selectedSeries.value) return;
        try {
          const response = await axiosService.get(`email/series/titles/${selectedSeries.value}`, { skipUserId: true });
          emailSeries.value = response.data; // assuming data is the array of email series
          errorMessage.value = '';
        } catch (error) {
          errorMessage.value = 'Failed to fetch series data';
          // Use alert or error service here if preferred
          console.error(error);
        }
      };
  
      const goToEmailDetails = (sequence, series) => {
        // Assuming your router is properly set up
        this.$router.push(`/email/series/${series}/${sequence}`);
      };
  
      return {
        selectedSeries,
        emailSeries,
        seriesOptions,
        fetchSeriesData,
        errorMessage,
        goToEmailDetails
      };
    }
  };
  </script>
  
  <style scoped>
  .error {
    color: red;
    margin-top: 10px;
  }
  </style>
  