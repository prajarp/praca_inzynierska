<template>
  <nav class="flex items-center justify-between px-6 py-4 bg-neutral-700">
    <ul class="flex items-center space-x-8">
      <li>
        <div class="relative text-white">
          <a 
            href="/orders/list" 
            :class="{'active-link': currentPath === '/orders/list'}"
          >
            Stwórz listę zamówień
          </a>
          <div 
            v-if="currentPath === '/orders/list'"
            class="absolute left-0 bottom-0 h-[3px] bg-orange-500 transition-all duration-300"
          ></div>
        </div>
      </li>
      <li>
        <div class="relative text-white">
          <a 
            href="/orders" 
            :class="{'active-link': currentPath === '/orders'}"
          >
            Zamówienia
          </a>
          <div 
            v-if="currentPath === '/orders'"
            class="absolute left-0 bottom-0 h-[3px] bg-orange-500 transition-all duration-300"
          ></div>
        </div>
      </li>
      <li>
        <div class="relative text-white">
          <a 
            href="/packing" 
            :class="{'active-link': currentPath === '/packing'}"
          >
            Pakowanie
          </a>
          <div 
            v-if="currentPath === '/packing'"
            class="absolute left-0 bottom-0 h-[3px] bg-orange-500 transition-all duration-300"
          ></div>
        </div>
      </li>
    </ul>
  </nav>
</template>

<script setup>
// Aktualny path
import { ref, onMounted } from 'vue';

const currentPath = ref('');

onMounted(() => {
  currentPath.value = window.location.pathname;
});
</script>

<style scoped>
/* Klasa dla aktywnego linku */
.active-link {
  color: #f97316; /* Pomarańczowy kolor */
  font-weight: bold;
  text-decoration: underline;
}
</style>
