import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura';
import 'primeicons/primeicons.css'
import PageLayout from './layouts/PageLayout.vue';
import Icon from './components/icons/Icon.vue';
import Box from './components/Box/Box.vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy'


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./pages/**/*.vue', { eager: true })
    let page = pages[`./pages/${name}.vue`]
    page.default.layout = page.default.layout || PageLayout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .component('Icon', Icon)
      .component('Link', Link)
      .component('Box', Box)
      .use(plugin)
      .use(ZiggyVue)
      .use(PrimeVue, {
        theme: {
          preset: Aura
        }
      })
      .mount(el)
  },
})
