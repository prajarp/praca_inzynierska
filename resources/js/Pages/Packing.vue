<template>
  <Navbar></Navbar>
  <div class="flex flex-1 overflow-hidden bg-gray-100">
    <div class="w-full p-4 overflow-auto">
      <div class="max-w-7xl mx-auto font-sans text-gray-800">
        <div class="border-solid border-4 border-gray-900">
        <div class="text-center mb-4">
          <div v-for="(row, rowIndex) in groupedMatrix" :key="'row-' + rowIndex" class="flex gap-4 mt-4">
            <div v-for="(col, colIndex) in row" :key="'col-' + colIndex" class="space-y-2">
              <div v-if="col.bin"
  class="relative p-4 border border-gray-300 rounded-lg bg-blue-400 h-56 flex flex-col justify-start shadow-md">
  
  <!-- Przycisk w prawym górnym rogu -->
  <div v-tippy="{
        content: tooltipContent(col),
        theme: 'light',
        allowHTML: true,
        placement: 'top'
      }">
    <button
      class="text-white text-sm absolute top-2 right-2 px-2 py-1 bg-blue-300 font-semibold rounded-md hover:bg-blue-200 transition-colors duration-200">
      Zawartość zamówienia
    </button>
  </div>

  <!-- Zawartość przesunięta na dół -->
  <div class="mt-auto self-start text-left">
    <p class="text-sm text-white"><strong>Bin ID:</strong> {{ col.bin.id }}</p>
    <p class="text-sm text-white"><strong>Client Name:</strong> {{ col.info.client_name }}</p>
    <p class="text-sm text-white"><strong>Address:</strong> {{ col.info.delivery_address }}</p>
  </div>
</div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
  <div class="flex flex-row w-full">
    <div class="w-1/2 p-4 flex flex-col items-center justify-center">
      <table class="table-fixed border-collapse border border-gray-300 w-2/4 text-sm mx-auto">
        <thead>
          <tr>
            <th class="border border-gray-300 px-2 py-1">Udźwig pojazdu</th>
            <th class="border border-gray-300 px-2 py-1">Waga zamówień</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="border border-gray-300 px-2 py-1">{{ tir.max_weight }} kg</td>
            <td class="border border-gray-300 px-2 py-1">{{ chartData.totalWeight }} kg</td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4 h-[150px] flex items-center justify-center">
        <canvas ref="chartCanvas" class="w-full h-full"></canvas>
      </div>
    </div>
    <div class="w-1/2 p-4 flex flex-col items-center justify-center">
      <table class="table-fixed border-collapse border border-gray-300 w-2/4 text-sm mx-auto">
        <thead>
          <tr>
            <th class="border border-gray-300 px-2 py-1">Pojemność naczepy</th>
            <th class="border border-gray-300 px-2 py-1">Pojemność zamówień</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="border border-gray-300 px-2 py-1">
              {{ Math.round(chartData.maxVolume).toFixed(2) }} m³
            </td>
            <td class="border border-gray-300 px-2 py-1">
              {{ Math.round(chartData.totalVolume).toFixed(2) }} m³
            </td>
          </tr>
        </tbody>
      </table>
      <div class="mt-4 h-[150px] flex items-center justify-center">
        <canvas ref="volumeChart" class="w-full h-full"></canvas>
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
  if (row && row.bin) {
    return `
      <strong>Waga zamówieniat:</strong> ${row.bin.totalFittedWeight + row.bin.weight} kg<br />
      <strong>Objętość zamówienia:</strong> ${row.bin.totalFittedVolume + row.bin.volume} m³<br />
      <h5>Informacja o oknach:</h5>
      <ul>
       ${row.bin.fittedItems.map(item => `<li>${item.id} (Weight: ${item.weight} kg, Volume: ${item.volume} m³)</li>`).join('')}
      </ul>
    `;
  }

  return 'No details available';
};

const trailerData = {
  total_volume: (props.tir.height * props.tir.width * props.tir.length).toFixed(2),
  total_weight: props.tir.max_weight,
  matrix: props.trailer.matrix,
};
console.Console

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

const groupedMatrix = computed(() => {
  return chunkArray(props.trailer.matrix, Math.ceil(props.trailer.matrix.length / 3));
});
</script>
