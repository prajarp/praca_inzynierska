<template>
  <Navbar></Navbar>
  <div class="flex w-full">
    <div id="selectedItems" class="w-1/4 bg-gray-200 py-2 px-6">
      <div class="flex flex-col items-center justify-center border rounded-lg">
        <div class="w-full max-w-2xl bg-neutral-50 flex items-center justify-between py-4 px-4 rounded-t-lg mb-2">
          <p class="font-bold text-black text-xl">Wybrane zamówienia</p>
          <button class="text-white bg-green-600 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2 text-center" @click="sendToAnotherView">
            Stwórz trasę
          </button>
          <button @click="clearList"
            class="text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-4 focus:ring-yellow-400 font-medium rounded-full text-sm px-5 py-2 text-center">Wyczyść
            listę</button>
        </div>

        <!-- Lista zamówień -->
        <ul v-if="selectedOrders.length" class="w-full max-w-2xl bg-gray-200">
          <li v-for="order in selectedOrders" :key="order.id" class="list-none mb-2">
            <div class="flex border rounded-lg shadow-md overflow-hidden">
              <div class="w-3/4 p-4 bg-neutral-50">
                <p>
                  <strong>Klient:</strong> {{ order.client_name }} <br />
                  <strong>Adres:</strong> {{ order.delivery_address }} <br />
                  <strong>Województwo:</strong> {{ order.voivodeship }} <br />
                  <strong>Data dostawy:</strong> {{ order.expected_delivery_date }}
                </p>
              </div>
              <div class="w-1/4 flex items-center justify-center bg-neutral-50">
                <button @click="removeOrder(order.id)"
                  class="text-white bg-red-600 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2 text-center">
                  Usuń
                </button>
              </div>
            </div>
          </li>
        </ul>

        <!-- Brak zamówień -->
        <p v-else class="text-center w-full max-w-2xl bg-neutral-50 p-4">Brak zaznaczonych zamówień.</p>
      </div>
    </div>

    <div id="remainingSpace" class="w-3/4 bg-gray-200 p-4">
      <div class="flex gap-4 mb-4">
        <div>
          <label class="block text-gray-700">Sortuj po dacie dostawy:</label>
          <select v-model="filters.expected_delivery_date" @change="applyFilters"
            class="mt-1 block w-full px-4 py-2 border-gray-300">
            <option value="">Wszystkie</option>
            <option value="asc">Rosnąco</option>
            <option value="desc">Malejąco</option>
          </select>
        </div>
        <div>
          <label class="block text-gray-700">Filtruj po województwie:</label>
          <select v-model="filters.voivodeship" @change="applyFilters"
            class="mt-1 block w-full px-4 py-2 border-gray-300">
            <option value="">Wszystkie</option>
            <option v-for="region in voivodeships" :key="region" :value="region">
              {{ region }}
            </option>
          </select>
        </div>
      </div>

      <table class="min-w-full bg-neutral-50">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left">
              <input type="checkbox" @change="toggleAll" :checked="isAllSelected" />
            </th>
            <th class="px-6 py-3 text-left">ID</th>
            <th class="px-6 py-3 text-left">
            <span class="font-bold">KLIENT</span>
            <span class="text-xs text-gray-500 italic">(podgląd zamówienia)</span>
          </th>
            <th class="px-6 py-3 text-left">ADRES DOSTAWY</th>
            <th class="px-6 py-3 text-left">WOJEWÓDZTWO</th>
            <th class="px-6 py-3 text-left">DATA DOSTAWY</th>
            <th class="px-6 py-3 text-left">WAGA</th>
            <th class="px-6 py-3 text-left">POWIERZCHNIA</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in props.orders.data" :key="order.id">
            <td class="px-6 py-4">
              <input type="checkbox" :value="order" v-model="selectedOrders" />
            </td>
            <td class="px-6 py-4">{{ order.id }}</td>
            <div v-tippy="{
              content: tooltipContent(order),
              theme: 'light',
              allowHTML: true,
              placement: 'top'
            }">
              <td class="px-6 py-4">{{ order.client_name }}</td>
            </div>
            <td class="px-6 py-4">{{ order.delivery_address }}</td>
            <td class="px-6 py-4">{{ order.voivodeship }}</td>
            <td class="px-6 py-4">{{ order.expected_delivery_date }}</td>
            <td class="px-6 py-4">{{ sumWeight(order) }} kg</td>
            <td class="px-6 py-4">{{ sumVolume(order) }} m²</td>
          </tr>
        </tbody>
      </table>

      <div class="mt-4 flex justify-between items-center">
        <button :disabled="pagination.current_page === 1" @click="goToPage(pagination.current_page - 1)"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-400 text-white disabled:opacity-50">
          Poprzednia
        </button>

        <span class="text-gray-600">
          Strona {{ pagination.current_page }} z {{ pagination.last_page }}
        </span>

        <button :disabled="pagination.current_page === pagination.last_page"
          @click="goToPage(pagination.current_page + 1)"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-400 text-white disabled:opacity-50">
          Następna
        </button>
      </div>
      <div class="flex flex-row w-full">
        <div class="w-1/2 p-4">
          <table class="table-fixed border-collapse border border-gray-300 w-full text-sm">
            <thead>
              <tr>
                <th class="border border-gray-300 px-2 py-1">Udźwig pojazdu</th>
                <th class="border border-gray-300 px-2 py-1">Waga zamówień</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border border-gray-300 px-2 py-1">{{ trailer.max_weight }} kg</td>
                <td class="border border-gray-300 px-2 py-1">{{ totalOrdersWeight }} kg</td>
              </tr>
            </tbody>
          </table>
          <div class="mt-4 h-[150px] flex items-center justify-center">
            <canvas ref="chartCanvas" class="w-full h-full"></canvas>
          </div>
        </div>
        <div class="w-1/2 p-4">
          <table class="table-fixed border-collapse border border-gray-300 w-full text-sm">
            <thead>
              <tr>
                <th class="border border-gray-300 px-2 py-1">Pojemność naczepy</th>
                <th class="border border-gray-300 px-2 py-1">Pojemność zamówień</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="border border-gray-300 px-2 py-1">
                  {{ (Math.round(trailer.length * trailer.width * trailer.height * 100) / 100).toFixed(2) }} m³
                </td>
                <td class="border border-gray-300 px-2 py-1">
                  {{ (Math.round(totalOrdersVoume * 100) / 100).toFixed(2) }} m³
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
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { calculateChartData, initializeWeightChart, initializeVolumeChart, updateCharts } from '../charts/selectionChart';
import Navbar from '../components/Navbar.vue';
import VueTippy from 'vue-tippy'

const props = defineProps({
  orders: Object,
  pagination: Object,
  voivodeships: Array,
  filtersFromServer: Object,
  trailer: Object,
});

const chartCanvas = ref(null);
const volumeChart = ref(null);
let weightChart = null;
let volumeChartInstance = null;
let totalOrdersWeight = ref(null);
let totalOrdersVoume = ref(null);

const selectedOrders = ref([]);

onMounted(() => {
  const storedOrders = JSON.parse(sessionStorage.getItem('selectedOrders') || '[]');
  selectedOrders.value = storedOrders;
  const chartData = calculateChartData(selectedOrders, { trailer: props.trailer });
  weightChart = initializeWeightChart(chartCanvas.value, chartData);
  volumeChartInstance = initializeVolumeChart(volumeChart.value, chartData);
});

watch(selectedOrders, (newVal) => {
  sessionStorage.setItem('selectedOrders', JSON.stringify(newVal));
  const chartData = calculateChartData(selectedOrders, { trailer: props.trailer });
  updateCharts(weightChart, volumeChartInstance, chartData);
  totalOrdersWeight = chartData.totalWeight;
  totalOrdersVoume = chartData.totalVolume;
});

const filters = reactive({
  expected_delivery_date: props.filtersFromServer.expected_delivery_date || '',
  voivodeship: props.filtersFromServer.voivodeship || '',
});

const applyFilters = () => router.get('/orders/list', { ...filters });
const goToPage = (page) => router.get('/orders/list', { page, ...filters });

const removeOrder = (orderId) => {
  selectedOrders.value = selectedOrders.value.filter((order) => order.id !== orderId);
};

const clearList = () => {
  selectedOrders.value = [];
};

const isOrderSelected = (orderId) => selectedOrders.value.some((order) => order.id === orderId);

const isAllSelected = computed(() =>
  props.orders.data.every((order) => isOrderSelected(order.id))
);

const toggleAll = () => {
  if (isAllSelected.value) {
    selectedOrders.value = selectedOrders.value.filter(
      (order) => !props.orders.data.some((o) => o.id === order.id)
    );
  } else {
    const newOrders = props.orders.data.filter(
      (order) => !isOrderSelected(order.id)
    );
    selectedOrders.value = [...selectedOrders.value, ...newOrders];
  }
};

const sumWeight = (order) => {
  if (!order.order_items || !Array.isArray(order.order_items)) return 0;
  return order.order_items.reduce((total, item) => total + (item.weight || 0), 0);
};

const sumVolume = (order) => {
  if (!order.order_items || !Array.isArray(order.order_items)) return 0;
  return order.order_items.reduce((total, item) => {
    const height = item.height || 0;
    const width = item.width || 0;
    const length = item.length || 0;
    return total + height * width * length;
  }, 0);
};

const tooltipContent = (order) => {
  console.log(order.order_items);
  // if (order) {
  return `
      <strong>Zawartość zamówień:</strong> ${order.order_items.map(item => `<li>${item.id}, (Item Type: ${item.item_type}), (Weight: ${item.weight} kg,)</li>`).join('')}
    `;
  // }

  // return 'No details available';
};

const sendToAnotherView = () => {
  router.post('/orders/selected', { selected: selectedOrders.value });
};
</script>
