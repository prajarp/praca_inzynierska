<template>
  <Navbar></Navbar>

    <!-- <Section></Section> -->
    <div class="flex flex-1 overflow-hidden bg-gray-100">
      <!-- Pierwszy kontener - 1/4 szerokości strony -->
      <div class="w-1/4 p-4 overflow-auto">
        <table class="table-fixed border-collapse border border-gray-300 w-full text-sm">
          <thead>
            <tr>
              <th class="border border-gray-300 px-2 py-1">Vehicle Max Weight</th>
              <th class="border border-gray-300 px-2 py-1">Total Order Weight</th>
              <th class="border border-gray-300 px-2 py-1">Vehicle Max Volume</th>
              <th class="border border-gray-300 px-2 py-1">Total Order Volume</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border border-gray-300 px-2 py-1">{{ tir.max_weight }} kg</td>
              <td class="border border-gray-300 px-2 py-1">{{ chartData.totalWeight }} kg</td>
              <td class="border border-gray-300 px-2 py-1">{{ chartData.maxVolume }} m³</td>
              <td class="border border-gray-300 px-2 py-1">{{ chartData.totalVolume }} m³</td>
            </tr>
          </tbody>
        </table>
        <div class="mt-4">
          <div class="flex gap-2 mt-4 h-40">
            <div class="flex-1">
              <canvas ref="chartCanvas" class="w-full h-full"></canvas>
            </div>
            <div class="flex-1">
              <canvas ref="volumeChart" class="w-full h-full"></canvas>
            </div>
          </div>
        </div>
      </div>
      <!-- Drugi kontener - 3/4 szerokości strony -->
      <div class="w-3/4 p-4 overflow-auto">
  <div class="max-w-7xl mx-auto font-sans text-gray-800">
    <div class="text-center mb-4">
      <div class="bg-gray-600">
        <p class="text-white text-sm">Sides</p>
      </div>
      <!-- Wyświetlanie elementów w 3 rzędach -->
      <div v-for="(row, rowIndex) in groupedMatrix" :key="'row-' + rowIndex" class="flex gap-4 justify-center mt-4">
        <div v-for="(col, colIndex) in row" :key="'col-' + colIndex" class="space-y-2">
          <div v-if="col.bin"
            class="p-2 text-center border border-gray-300 rounded-lg bg-blue-50 h-56 flex flex-col items-center justify-center">
            <p class="text-sm"><strong>Bin ID:</strong> {{ col.bin.id }}</p>
            <p class="text-sm"><strong>Client Name:</strong> {{ col.info.client_name }}</p>
            <p class="text-sm"><strong>Address:</strong> {{ col.info.delivery_address }}</p>
            <div v-tippy="{
              content: tooltipContent(col),
              theme: 'light',
              allowHTML: true,
              placement: 'top'
            }">
              <button class="px-2 py-1 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                Show Details
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="bg-gray-600 mt-2">
        <p class="text-white text-sm">Sides</p>
      </div>
    </div>
  </div>
</div>
    </div>
</template>
<script setup>
import Chart from 'chart.js/auto';
import Navbar from '../components/Navbar.vue';
import Section from '../components/Section.vue';
import VueTippy from 'vue-tippy'
import { computed } from 'vue';
import { calculateChartData } from '../charts/packingChart';
import { onMounted } from 'vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';


const props = defineProps({
  trailer: {
    type: Object,
    required: true,
  },
  tir: {
    type: Object,
    required: true,
  },
});

const tooltipContent = (row) => {
  console.log(row);
  if (row && row.bin) {
    return `
      <strong>Total Fitted Weight:</strong> ${row.bin.totalFittedWeight} kg<br />
      <strong>Total Fitted Weight:</strong> 55 kg<br />
      <strong>Total Fitted Volume:</strong> ${row.bin.totalFittedVolume} m³<br />
      <h5>Fitted Items:</h5>
      <ul>
        ${row.bin.fittedItems.map(item => `<li>${item.id} (Weight: ${item.weight} kg, Volume: ${item.volume} m³)</li>`).join('')}
      </ul>
    `;
  }

  return 'No details available';
};

const trailerData = {
  total_volume: props.trailer.total_volume,
  total_weight: props.tir.max_weight,
  matrix: props.trailer.matrix,
};

const chartData = calculateChartData(trailerData);

const chartCanvas = ref(null);
const volumeChart = ref(null);

onMounted(() => {
  if (chartCanvas.value) {
    new Chart(chartCanvas.value, {
      type: 'pie',
      data: {
        labels: ['Pozostała waga', 'Waga zamówień'],
        datasets: [
          {
            data: [chartData.remainingTrailerWeight, chartData.totalWeight],
            backgroundColor: chartData.colors,
            borderColor: 'white',
            borderWidth: 5
          },
        ],
      },
      options: {
        cutoutPercentage: 50,
        responsive: true,
        maintainAspectRatio: false,
      },
    });
    new Chart(volumeChart.value, {
      type: 'pie',
      data: {
        labels: ['Pozostała objętość', 'Objętość zamówień'],
        datasets: [
          {
            data: [chartData.remainingTrailerVolume, chartData.totalVolume],
            backgroundColor: chartData.colors,
            borderColor: 'white',
            borderWidth: 5
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    });
  }
});

function chunkArray(array, size) {
  const result = [];
  for (let i = 0; i < array.length; i += size) {
    result.push(array.slice(i, i + size));
  }
  return result;
}

// Komputowana właściwość tworząca grupy
const groupedMatrix = computed(() => {
  return chunkArray(props.trailer.matrix, Math.ceil(props.trailer.matrix.length / 3));
});
</script>