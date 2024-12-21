import { createRouter, createWebHistory } from 'vue-router';

import Section from '../components/Section.vue';
import Navbar from '../components/Navbar.vue';
import Packing from '../Pages/Packing.vue';
import Orders from '../Pages/Orders.vue';
import OrdersSummary from '../Pages/OrdersSummary.vue';
import OrderSelection from '../Pages/OrderSelection.vue';
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
  {
    path: '/orders/selected',
    name: 'ordersSummary',
    component: OrdersSummary,
    meta: {title: 'Wybrane zamówienia'}
  },
  {
    path: '/orders/list',
    name: 'orderSelection',
    component: OrderSelection,
    meta: {title: 'Lista zamówień'}
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