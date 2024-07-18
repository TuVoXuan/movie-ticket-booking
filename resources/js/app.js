import './bootstrap';

import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import PageLayout from './layouts/PageLayout.vue';
import Icon from './components/icons/Icon.vue';
import Box from './components/Box/Box.vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy'
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    let page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || PageLayout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(Antd)
      .component('Icon', Icon)
      .component('Link', Link)
      .component('Box', Box)
      .mount(el)
  },
})
