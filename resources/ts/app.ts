import './bootstrap'; // Подключение bootstrap.js
import Index from './Index.vue';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/styles';
import importer from "./importer/importer";
import storage from "./storage/storage";
import 'viewerjs/dist/viewer.css'
import VueViewer from 'v-viewer'
import { createApp } from 'vue';
import router from '@/router/router';  // Подключаем маршрутизатор

import Echo from "laravel-echo";
declare global {
    interface Window {
        Echo: Echo<any>;
    }
}
const app = createApp(Index);

importer.forEach((component) => {
    if (component.name) {
        app.component(component.name, component);
    }
});


const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'light',
    },
});
app.use(router);  // Подключаем маршрутизатор к приложению

app.use(VueViewer)
app.use(vuetify);
app.use(storage);
app.mount('#app');
