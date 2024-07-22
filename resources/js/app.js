import './bootstrap.js';
import {createApp} from "vue/dist/vue.esm-bundler.js";
import { createI18n } from 'vue-i18n';
import en from './len/en.json';
import nl from './len/nl.json';
import router from "./router/index.js";
import store from "./store/index.js";
import MainComponent from "./components/Layout/MainComponent.vue";

const messages = {en,nl};

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages,
});

const app = createApp({
    components: {
        MainComponent: MainComponent,
    }
});

app.use(i18n);
app.use(router);
app.use(store);
app.mount('#app');

 

