<template>
  <v-container class="my-12">
    <h1 class="text-center text-text mb-8 font-weight-bold">
      Lapas statistika
    </h1>

    <!-- Kartīnas: lietotāji un kolektīvi -->
    <v-row justify="center" class="mb-10" dense>
      <!-- Lietotāju kartiņa -->
      <v-col cols="12" sm="6" md="3">
        <v-card class="pa-6 text-center elevation-6 user-card">
          <div class="text-h2 font-weight-bold mb-2">{{ users.length }}</div>
          <div class="text-subtitle-1 text-gray-100">Kopējais lietotāju skaits</div>
        </v-card>
      </v-col>

      <!-- Kolektīvu kartiņa -->
      <v-col cols="12" sm="6" md="3">
        <v-card class="pa-6 text-center elevation-6 group-card">
          <div class="text-h2 font-weight-bold mb-2">{{ groups.length }}</div>
          <div class="text-subtitle-1 text-gray-100">Deju kolektīvu skaits</div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Diagramma 1: kolektīvu statusi -->
    <v-card class="pa-6 mx-auto mb-10" max-width="700" elevation="6">
      <canvas id="statusChart"></canvas>
    </v-card>

    <!-- Diagramma 2: dejotāju skaits pa grupām -->
    <v-card class="pa-6 mx-auto" max-width="900" elevation="6">
      <canvas id="dancersChart"></canvas>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios';
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
} from 'chart.js';

Chart.register(
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Title,
  Tooltip,
  Legend
);

export default {
  data() {
    return { 
      groups: [],
      users: []
    };
  },
  mounted() {
    this.fetchUsers();
    this.fetchGroupsAndRenderCharts();
  },
  methods: {
    fetchGroupsAndRenderCharts() {
      fetch('/danceGroups-all')
        .then(res => res.json())
        .then(data => {
          this.groups = Array.isArray(data.data) ? data.data : [];

          // --- Diagramma 1: statusi ---
          const total = this.groups.length;
          const approved = this.groups.filter(g => g.status === 'approved').length;
          const declined = this.groups.filter(g => g.status === 'declined').length;
          const pending = this.groups.filter(g => g.status === 'waiting').length;

          const ctxStatus = document.getElementById('statusChart').getContext('2d');
          const gradientStatus = ctxStatus.createLinearGradient(0, 0, 0, 400);
          gradientStatus.addColorStop(0, 'rgba(59,130,246,0.8)');
          gradientStatus.addColorStop(1, 'rgba(59,130,246,0.3)');

          new Chart(ctxStatus, {
            type: 'bar',
            data: {
              labels: ['Kopā', 'Gaidīšana', 'Apstiprināti', 'Noraidīti'],
              datasets: [{
                label: 'Deju kolektīvi',
                data: [total, pending, approved, declined],
                backgroundColor: [
                  gradientStatus,
                  'rgba(250,204,21,0.7)',
                  'rgba(16,185,129,0.7)',
                  'rgba(239,68,68,0.7)'
                ],
                borderRadius: 6,
                barPercentage: 0.6
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false },
                title: { display: true, text: 'Deju kolektīvu statuss', font: { size: 18 } }
              },
              scales: {
                y: { beginAtZero: true, precision: 0 },
                x: { grid: { display: false } }
              }
            }
          });

          // --- Diagramma 2: dejotāji pa grupām ---
          const labels = this.groups.map(g => g.name);
          const dancersCount = this.groups.map(g => g.dancers.length);

          const ctxDancers = document.getElementById('dancersChart').getContext('2d');
          const gradientDancers = ctxDancers.createLinearGradient(0, 0, 0, 400);
          gradientDancers.addColorStop(0, 'rgba(59,130,246,0.8)');
          gradientDancers.addColorStop(1, 'rgba(59,130,246,0.3)');

          new Chart(ctxDancers, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                label: 'Dejotāji grupā',
                data: dancersCount,
                backgroundColor: labels.map(() => gradientDancers),
                borderRadius: 6,
                barPercentage: 0.5
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false },
                title: { display: true, text: 'Dejotāju skaits pa grupām', font: { size: 18 } }
              },
              scales: {
                y: { beginAtZero: true, precision: 0 },
                x: { title: { display: true, text: 'Deju grupas' }, grid: { display: false } }
              }
            }
          });

        })
        .catch(err => console.error('Kļūda ielādējot datus:', err));
    },
    fetchUsers() {
      axios.get('/admin/users', { withCredentials: true })
        .then(res => { 
          this.users = Array.isArray(res.data.data) ? res.data.data : []; 
        })
        .catch(err => console.error(err));
    }
  }
};
</script>

<style scoped>
.v-card {
  border-radius: 16px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.12);
  transition: transform 0.2s ease-in-out;
}
.v-card:hover {
  transform: translateY(-4px);
}
.user-card {
  background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
  color: white;
}
.group-card {
  background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
  color: white;
}
h1 {
  font-size: 2rem;
}
</style>