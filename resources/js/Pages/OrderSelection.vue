<template>
    <div class="flex w-full h-screen">
    <!-- Wybrane Zamówienia -->
     <!-- Wybrane Zamówienia -->
     <div id="selectedItems" class="w-1/4 bg-gray-200 p-4">
      <h2 class="font-bold mb-2">Wybrane zamówienia</h2>
      <ul v-if="selectedOrders.length" class="list-disc pl-5">
        <li v-for="order in selectedOrders" :key="order.id">
          <p>
            <strong>Klient:</strong> {{ order.client_name }} <br />
            <strong>Adres:</strong> {{ order.delivery_address }} <br />
            <strong>Województwo:</strong> {{ order.voivodeship }} <br />
            <strong>Data dostawy:</strong> {{ order.expected_delivery_date }} <br />
          </p>
          <button @click="removeOrder(order.id)" class="text-red-500">Usuń</button>
        </li>
      </ul>
      <p v-else>Brak zaznaczonych zamówień.</p>
    </div>
        <div id="remainingSpace" class="w-3/4 bg-gray-100 p-4">
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
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" @change="toggleAll" :checked="isAllSelected" />
                        </th>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">KLIENT</th>
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
                        <td class="px-6 py-4">{{ order.client_name }}</td>
                        <td class="px-6 py-4">{{ order.delivery_address }}</td>
                        <td class="px-6 py-4">{{ order.voivodeship }}</td>
                        <td class="px-6 py-4">{{ order.expected_delivery_date }}</td>
                        <td class="px-6 py-4">{{ order.total_weight }} kg</td>
                        <td class="px-6 py-4">{{ order.window_area }} m²</td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 flex justify-between items-center">
                <button :disabled="pagination.current_page === 1" @click="goToPage(pagination.current_page - 1)"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:opacity-50">
                    Poprzednia
                </button>

                <span class="text-gray-600">
                    Strona {{ pagination.current_page }} z {{ pagination.last_page }}
                </span>

                <button :disabled="pagination.current_page === pagination.last_page"
                    @click="goToPage(pagination.current_page + 1)"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 disabled:opacity-50">
                    Następna
                </button>
            </div>
            <div class="mt-4">
                <button class="px-4 py-2 bg-blue-500 text-white rounded" @click="sendToAnotherView">
                    Wyślij zaznaczone rekordy
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
  orders: Object,
  pagination: Object,
  voivodeships: Array,
  filtersFromServer: Object,
});

// Reaktywna lista zamówień
const selectedOrders = ref([]);

// Wczytaj zamówienia z SessionStorage
onMounted(() => {
  const storedOrders = JSON.parse(sessionStorage.getItem('selectedOrders') || '[]');
  selectedOrders.value = storedOrders;
});

// Obserwuj zmiany w liście i zapisuj do SessionStorage
watch(selectedOrders, (newVal) => {
  sessionStorage.setItem('selectedOrders', JSON.stringify(newVal));
});
// Obserwuj i zapisuj do SessionStorage
watch(selectedOrders, (newVal) => {
  sessionStorage.setItem('selectedOrders', JSON.stringify(newVal));
});

// Filtry i Paginacja
const filters = reactive({
  expected_delivery_date: props.filtersFromServer.expected_delivery_date || '',
  voivodeship: props.filtersFromServer.voivodeship || '',
});

// Funkcje
const applyFilters = () => router.get('/orders/list', { ...filters });
const goToPage = (page) => router.get('/orders/list', { page, ...filters });

// Dodaj lub Usuń Zamówienie
const toggleOrder = (order) => {
  const exists = selectedOrders.value.find((o) => o.id === order.id);
  if (exists) {
    selectedOrders.value = selectedOrders.value.filter((o) => o.id !== order.id);
  } else {
    selectedOrders.value.push(order);
  }
};

// Usuń Zamówienie
const removeOrder = (orderId) => {
  selectedOrders.value = selectedOrders.value.filter((order) => order.id !== orderId);
};

// Sprawdź, czy zamówienie jest zaznaczone
const isOrderSelected = (orderId) => selectedOrders.value.some((order) => order.id === orderId);

// Zaznacz lub Odznacz Wszystkie
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

// Wyślij Zaznaczone Rekordy
const sendToAnotherView = () => {
  router.post('/orders/selected', { selected: selectedOrders.value });
};
</script>