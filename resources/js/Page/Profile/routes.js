import { watchEffect } from "vue";
import store from "../../store";
import i18n from "../../i18n";
import ProfileComponent from "./ProfileComponent.vue";

export default [
    {
        path: '/profile',
        name: 'profile',
        component: ProfileComponent,
        meta: { requiresAuth: true, title: 'Profile' },
    }
];