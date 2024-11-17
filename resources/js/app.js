import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';  // Import ZiggyVue
import '../css/app.css';

// Ustaw Ziggy jako globalną zmienną


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    return pages[`./Pages/${name}.vue`];  // Poprawna interpolacja
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });

    app.use(plugin)
       .use(ZiggyVue)  // Dodaj ZiggyVue jako plugin globalny
       .mount(el);
  },
});