// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';

// Importowanie komponentów widoków
import Section from '../components/Section.vue';
import Navbar from '../components/Navbar.vue';
import Packing from '../Pages/Packing.vue';
import Orders from '../Pages/Orders.vue';

const routes = [
  {
    path: '/secion',
    name: 'secion',
    component: Section
  },
  {
    path: '/navbar',
    name: 'navbar',
    component: Navbar
  },
  {
    path: '/packing',
    name: 'packing',
    component: Packing,
    meta: {title: 'Packing'},
  },
  {
    path: '/orders',
    name: 'orders',
    component: Orders,
    meta: {title: 'Orders'},
  },
];


const router = createRouter({
  history: createWebHistory(),
  routes 
});

export default router;

router.beforeEach((to, from) => {
  document.title = to.meta?.title ?? 'Default Title'
})