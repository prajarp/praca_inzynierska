import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import '../css/app.css';
import router from './router';

import VueTippy from 'vue-tippy';
import 'tippy.js/dist/tippy.css';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
    return pages[`./Pages/${name}.vue`];
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) });

    app.use(plugin)
       .use(ZiggyVue)
       .use(VueTippy)
       .use(router)
       .mount(el);
  },
});