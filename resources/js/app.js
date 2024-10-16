import './bootstrap.js';
import { createApp } from "vue/dist/vue.esm-bundler.js";
import { APP_VARIABLES } from './constants.js';
import router from "./router/index.js";
import store from "./store/index.js";
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import MainComponent from "./components/Layout/MainComponent.vue";
import './../../public/tinymce/tinymce.min.js'
import i18n from './i18n.js';

const app = createApp({
    components: {
        MainComponent: MainComponent,
    }
});

app.use(VueSweetalert2);

app.config.globalProperties.$constants = {
    APP_VARIABLES
};

app.use(i18n);
app.use(router);
app.use(store);
app.mount('#app');



