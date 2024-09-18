<template>
    <div>
      <label for="email-series">Select an Email Series:</label>
      <select v-model="selectedSeries" @change="fetchSeriesData">
        <option v-for="series in seriesOptions" :key="series.name" :value="series.name">
          {{ series.public_name }}
        </option>
      </select>
  
      <table v-if="emailSeries.length" class="email-table">
        <thead>
          <tr>
            <th>Sequence</th>
            <th>Subject Line</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(email, index) in emailSeries" :key="index" @click="goToEmailDetails(email.sequence, selectedSeries)">
            <td>{{ email.sequence }}</td>
            <td>{{ email.subject }}</td>
          </tr>
        </tbody>
      </table>
      <button @click="addEmailToSeries(selectedSeries)">Add Email to Series</button>
  
      <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue';
  import axiosService from '@/services/AxiosService'; // assuming you have a service file
  import { useRouter } from 'vue-router'; // Import the router
  export default {
    name: 'EmailSeriesTitles',
    setup() {
      const router = useRouter(); // Use the router
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
        { name: 'Tracts', public_name: 'Tracts' },
        { name: 'Blog', public_name: 'Blog' }
      ];
  
      const fetchSeriesData = async () => {
        if (!selectedSeries.value) return;
        try {
          const response = await axiosService.get(`email/series/titles/${selectedSeries.value}`, { skipUserId: true });
          console.log (response)
          if (response.data.success == false){
            errorMessage.value = response.data.message;
            emailSeries.value = [];
          }else{
            errorMessage.value = '';
            emailSeries.value = response.data.data; // Assuming the data is an array of objects
          }
        } catch (error) {
          errorMessage.value = 'Failed to fetch series data';
          // Use alert or error service here if preferred
          console.error(error);
        }
      };
  
      // Function to navigate to email details
      const goToEmailDetails = (sequence, series) => {
        router.push(`/email/series/${series}/${sequence}`); // Navigate using the router
      };
      
      const addEmailToSeries = (series) => {
        // Check if emailSeries.value exists and has a length, otherwise default to 0
        const nextEmail = emailSeries.value && Array.isArray(emailSeries.value) ? emailSeries.value.length + 1 : 1;

        // Navigate using the router
        router.push(`/email/series/${series}/${nextEmail}`);
      };

      return {
        selectedSeries,
        emailSeries,
        seriesOptions,
        fetchSeriesData,
        errorMessage,
        goToEmailDetails,
        addEmailToSeries
      };
    }
  };
  </script>
  
  <style scoped>
  .error {
    color: red;
    margin-top: 10px;
  }

.email-table {
  width: 100%;
  border-collapse: collapse;
  margin: 20px 0;
}

.email-table th, .email-table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left; /* Ensure text is left-aligned */
}

.email-table th {
  background-color: #f2f2f2;
  font-weight: bold;
}

.email-table tr {
  cursor: pointer;
}

.email-table tr:hover {
  background-color: #f1f1f1; /* Highlight row on hover */
}

  </style>
  