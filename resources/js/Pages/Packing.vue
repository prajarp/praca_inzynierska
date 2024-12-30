<template>
  <Navbar></Navbar>
  <div class="flex flex-1 overflow-hidden bg-gray-100">
    <div class="w-1/4 p-4 bg-gray-100 flex items-center justify-center">
      <div class="flex flex-col items-center justify-center border rounded-lg w-full">
        <div class="w-full max-w-2xl bg-neutral-50 flex items-center justify-between py-4 px-4 rounded-t-lg mb-2">
          <p class="font-bold text-black text-xl">Trasa</p>
        </div>
        <ul class="w-full">
          <li v-for="(order, index) in props.orderList" :key="order" class="list-none mb-2 w-full">
            <div class="flex border rounded-lg shadow-xl overflow-hidden w-full">
              <div class="w-full p-4 bg-greutral-500">
                <p>
                  <strong>{{ index + 1 }}. </strong> {{ order.address }} <br />
                </p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="w-full p-4 overflow-auto">
      <div class="max-w-7xl mx-auto font-sans text-gray-800">
        <div class="flex items-center justify-center bg-gray-400 ">
          <p>Bok naczepy</p>
        </div>
        <div class="border-solid border-4 border-gray-900">
          <div class="text-center ml-4 mb-4 mt-4">
            <div class="flex space-x-2 mb-4">
              <div v-for="(item, index) in firstRow" :key="index"
                class="space-y-2 w-[200px] h-[150px] md:w-[250px] md:h-[200px]">
                <div v-if="item"
                  class="relative p-4 border border-gray-300 rounded-lg bg-blue-400 flex flex-col justify-start shadow-md w-full h-full">
                  <div v-tippy="{
                    content: tooltipContent(item.bin),
                    theme: 'light',
                    allowHTML: true,
                    placement: 'top',
                  }">
                    <button
                      class="text-white text-sm absolute top-2 right-2 left-2 px-1 py-1 bg-blue-300 font-semibold rounded-md hover:bg-blue-200 transition-colors duration-200">
                      Zawartość
                    </button>
                  </div>
                  <div class="mt-auto self-start text-left">
                    <p class="text-sm text-white"><strong>Client Name:</strong> {{ item.info.client_name }}</p>
                    <p class="text-sm text-white"><strong>Address:</strong> {{ item.info.delivery_address }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex space-x-2 mb-4">
              <div v-for="(item, index) in thirdRow" :key="index"
                class="space-y-2 w-[200px] h-[150px] md:w-[250px] md:h-[200px]">
                <div v-if="item"
                  class="relative p-4 border border-gray-300 rounded-lg bg-blue-400 flex flex-col justify-start shadow-md w-full h-full">
                  <div v-tippy="{
                    content: tooltipContent(item.bin),
                    theme: 'light',
                    allowHTML: true,
                    placement: 'top',
                  }">
                    <button
                      class="text-white text-sm absolute top-2 right-2 left-2 px-1 py-1 bg-blue-300 font-semibold rounded-md hover:bg-blue-200 transition-colors duration-200">
                      Zawartość
                    </button>
                  </div>
                  <div class="mt-auto self-start text-left">
                    <p class="text-sm text-white"><strong>Client Name:</strong> {{ item.info.client_name }}</p>
                    <p class="text-sm text-white"><strong>Address:</strong> {{ item.info.delivery_address }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex space-x-2 mb-4" v-if="secondRow.length > 0">
              <div v-for="(item, index) in secondRow" :key="index"
                class="space-y-2 w-[200px] h-[150px] md:w-[250px] md:h-[200px]">
                <div v-if="item"
                  class="relative p-4 border border-gray-300 rounded-lg bg-blue-400 flex flex-col justify-start shadow-md w-full h-full">
                  <div v-tippy="{
                    content: tooltipContent(item.bin),
                    theme: 'light',
                    allowHTML: true,
                    placement: 'top',
                  }">
                    <button
                      class="text-white text-sm absolute top-2 right-2 left-2 px-1 py-1 bg-blue-300 font-semibold rounded-md hover:bg-blue-200 transition-colors duration-200">
                      Zawartość
                    </button>
                  </div>
                  <div class="mt-auto self-start text-left">
                    <p class="text-sm text-white"><strong>Client Name:</strong> {{ item.info.client_name }}</p>
                    <p class="text-sm text-white"><strong>Address:</strong> {{ item.info.delivery_address }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center justify-center bg-gray-400 ">
          <p>Bok naczepy</p>
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
                <td class="border border-gray-300 px-2 py-1">{{ vehicle.max_weight }} kg</td>
                <td class="border border-gray-300 px-2 py-1">{{ chartData.totalWeight }} kg</td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 h-[150px] flex items-center justify-center">
            <canvas ref="chartCanvas" class="w-full h-full"></canvas>
          </div>
        </div>
        <div class="w-1/2 p-4 flex flex-col items-center justify-center">
          <div>
            <table class="table-fixed border-collapse border border-gray-300 w-2/4 text-sm mx-auto">
              <thead>
                <tr>
                  <th class="border border-gray-300 px-2 py-1">Pojemność naczepy</th>
                  <th class="border border-gray-300 px-2 py-1">Objętość zamówień</th>
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
      </div>
    </div>

  </div>

</template>
<script setup>
import Chart from 'chart.js/auto';
import Navbar from '../components/Navbar.vue';
import { computed } from 'vue';
import { calculateChartData } from '../charts/packingChart';
import { onMounted } from 'vue';
import { ref } from 'vue';

const props = defineProps({
  trailer: {
    type: Object,
    required: true,
  },
  vehicle: {
    type: Object,
    required: true,
  },
  orderList: {
    type: Object,
    required: true,
  }
});

const BILION = 1000000000;

const firstRow = computed(() => {
  if (props.trailer.matrix.length < 10) {
    return props.trailer.matrix.slice(0, Math.ceil(props.trailer.matrix.length / 2));
  }
  // Standardowe dzielenie na trzy części
  const chunkSize = Math.ceil(props.trailer.matrix.length / 3);
  return props.trailer.matrix.slice(0, chunkSize);
});

const secondRow = computed(() => {
  if (props.trailer.matrix.length < 10) {
    return [];
  }
  const chunkSize = Math.ceil(props.trailer.matrix.length / 3);
  return props.trailer.matrix.slice(chunkSize, chunkSize + Math.floor(props.trailer.matrix.length / 3));
});

const thirdRow = computed(() => {
  if (props.trailer.matrix.length < 10) {
    return props.trailer.matrix.slice(Math.ceil(props.trailer.matrix.length / 2));
  }
  const chunkSize = Math.ceil(props.trailer.matrix.length / 3);
  return props.trailer.matrix.slice(chunkSize + Math.floor(props.trailer.matrix.length / 3)).reverse();
});


const tooltipContent = (row) => {
  if (row) {
    return `
      <strong>Waga zamówienia + stojak:</strong> ${row.totalFittedWeight + row.weight} kg<br />
      <strong>Objętość zamówienia:</strong> ${(row.volume) / BILION} m³<br />
      <h5>Informacja o oknach:</h5>
      <ul>
       ${row.fittedItems.map(item => `<li>${item.id} (Waga: ${item.weight} kg, Objętość: ${item.volume / BILION} m³)</li>`).join('')}
      </ul>
    `;
  }

  return 'No details available';
};

const trailerData = {
  total_volume: (props.vehicle.height * props.vehicle.width * props.vehicle.length).toFixed(2),
  total_weight: props.vehicle.max_weight,
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
