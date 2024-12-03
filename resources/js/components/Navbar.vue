<template>
    <nav class="bg-blue-600 p-4">
      <!-- Container -->
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <div class="text-white font-semibold text-xl">
          <!-- <router-link to="/" class="hover:text-gray-200">MyApp</router-link> -->
        </div>
  
        <!-- Menu (Desktop View) -->
        <div class="hidden md:flex space-x-6">
          <router-link :to="{ name: 'packing' }" class="text-white hover:text-gray-200">Packing</router-link>
          <router-link :to="{ name: 'orders' }" class="text-white hover:text-gray-200">Orders</router-link>
        </div>
  
        <!-- Hamburger Menu (Mobile View) -->
        <div class="md:hidden">
          <button @click="toggleMenu" class="text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
        </div>
      </div>
  
      <!-- Mobile Menu -->
      <div v-if="isMenuOpen" class="md:hidden bg-blue-500 text-white p-4 space-y-4">
        <!-- <router-link to="#" @click="closeMenu" class="block">Home</router-link>
        <router-link to="#" @click="closeMenu" class="block">About</router-link>
        <router-link to="#" @click="closeMenu" class="block">Services</router-link>
        <router-link to="#" @click="closeMenu" class="block">Contact</router-link> -->
      </div>
    </nav>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const isMenuOpen = ref(false);
  
  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  };
  
  const closeMenu = () => {
    isMenuOpen.value = false;
  };
  </script>