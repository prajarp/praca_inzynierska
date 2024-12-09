<template>
    <nav class="flex items-center justify-between px-6 py-4 bg-white">
      <ul class="flex items-center space-x-8">
        <li>
            <div class="relative">
                <router-link
                :to="{ name: 'packing' }"
                class="nav-item"
                @mouseenter="hoverPacking = true"
                @mouseleave="hoverPacking = false"
                >
                Packing
                </router-link>
                <div
                class="absolute left-0 bottom-0 h-[3px] bg-orange-500 transition-all duration-300"
                :class="{ 'w-full': hoverPacking || $route.name === 'packing', 'w-0': !hoverPacking && $route.name !== 'packing' }"
                ></div>
            </div>
            </li>
            <li>
            <div class="relative">
                <router-link
                :to="{ name: 'orders' }"
                class="nav-item"
                @mouseenter="hoverOrders = true"
                @mouseleave="hoverOrders = false"
                >
                Orders
                </router-link>
                <div
                class="absolute left-0 bottom-0 h-[3px] bg-orange-500 transition-all duration-300"
                :class="{ 'w-full': hoverOrders || $route.name === 'orders', 'w-0': !hoverOrders && $route.name !== 'orders' }"
                ></div>
            </div>
            </li>
      </ul>
    </nav>
  </template>
  
  <script>
  import { ref } from "vue";
  
  export default {
    setup() {
      const hoverActive = ref(false);
      const isActive = ref(true); // Ustaw na true, jeśli Kariera jest aktywnym widokiem
  
      return {
        hoverActive,
        isActive,
      };
    },
  };
  </script>
  
  <style scoped>
  .nav-item {
    @apply text-gray-700 font-medium transition-colors duration-300;
  }
  
  .nav-item:hover {
    @apply text-orange-500;
  }
  
  .nav-item.active {
    @apply text-orange-500;
  }
  </style>
  