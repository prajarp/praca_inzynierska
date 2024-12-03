<template>
  <Navbar></Navbar>
  <div class="flex">
    <Section></Section>
    <div class="max-w-7xl mx-auto font-sans text-gray-800">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-blue-600">Trailer Matrix</h1>
        <div class="bg-gray-600">
          <p class="text-white">Front</p>
        </div>
        <div class="grid grid-cols-3 gap-4 mt-8">
          <div v-for="(row, rowIndex) in trailer.matrix" :key="'row-' + rowIndex" class="space-y-4">
            <div v-for="(trailerItem, colIndex) in row" :key="'col-' + colIndex">
              <div v-if="trailerItem && trailerItem.bin"
                class="p-4 text-center border border-gray-300 rounded-lg bg-blue-50 h-96 flex flex-col items-center justify-center">
                <p><strong>Bin ID:</strong> {{ trailerItem.bin.id }}</p>
                <p><strong>Client Name:</strong> {{ row[0]?.info[0].client_name }}</p>
                <p><strong>Address</strong> {{ row[0]?.info[0].delivery_address }}</p>

                <div v-tippy="{
                  content: tooltipContent(trailerItem),
                  theme: 'light',
                  allowHTML: true,
                  placement: 'top'
                }">
                  <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Show Details
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-600">
          <p class="text-white">Back</p>
        </div>
      </div>


      <table class="table-fixed border-collapse border border-gray-300">
        <thead>
          <tr>
            <th class="border border-gray-300 px-4 py-2">Vehicle Max Weight</th>
            <th class="border border-gray-300 px-4 py-2">Total Order Weight</th>
            <th class="border border-gray-300 px-4 py-2">Vehicle Max Volume</th>
            <th class="border border-gray-300 px-4 py-2">Total Order Volume</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="border border-gray-300 px-4 py-2">{{ tir.max_weight }} kg </td>
            <td class="border border-gray-300 px-4 py-2">{{ chartData.totalWeight }} kg </td>
            <td class="border border-gray-300 px-4 py-2">{{ chartData.maxVolume }} m³ </td>
            <td class="border border-gray-300 px-4 py-2">{{ chartData.totalVolume }} m³ </td>
          </tr>
        </tbody>
      </table>

      <div class="flex gap-5 mt-8">
        <div class="flex-1 h-72">
          <canvas ref="chartCanvas" class="w-full h-full"></canvas>
        </div>
        <div class="flex-1 h-72">
          <canvas ref="volumeChart" class="w-full h-full"></canvas>
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
import { calculateChartData } from '../chartUtils.js';
import { onMounted } from 'vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';


const props = defineProps({
  // details: {
  //   type: Object,
  //   required: true,
  // },
  trailer: {
    type: Object,
    required: true,
  },
  tir: {
    type: Object,
    required: true,
  },
});

const tooltipContent = (trailerItem) => {
  if (trailerItem && trailerItem.bin) {
    return `
      <strong>Total Fitted Weight:</strong> ${trailerItem.bin.totalFittedWeight} kg<br />
      <strong>Total Fitted Volume:</strong> ${trailerItem.bin.totalFittedVolume} m³<br />
      <h5>Fitted Items:</h5>
      <ul>
        ${trailerItem.bin.fittedItems.map(item => `<li>${item.id} (Weight: ${item.weight} kg, Volume: ${item.volume} m³)</li>`).join('')}
      </ul>
    `;
  }
  return 'No details available';
};

// const details = props.details.matrix;
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
</script>