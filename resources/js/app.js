import './bootstrap.js';
import {createApp} from "vue/dist/vue.esm-bundler.js";
import { createI18n } from 'vue-i18n';
// import en from './locale/en.json';
// import nl from './locale/nl.json';
import en from './../../lang/en.json';
import { APP_VARIABLES } from './constants.js';   
import router from "./Router/index.js";
import store from "./Store/index.js";
import MainComponent from "./Components/Layout/MainComponent.vue";

const messages = {en};

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

app.config.globalProperties.$constants = {
    APP_VARIABLES  
};

app.use(i18n);
app.use(router);
app.use(store);
app.mount('#app');

 

