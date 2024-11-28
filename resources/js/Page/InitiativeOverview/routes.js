import { watchEffect } from "vue";
import store from "../../store";
import i18n from "../../i18n";
import InitiativeOverviewComponent from "./InitiativeOverviewComponent.vue";

export default [
    {
        path: '/initiative-overview',
        name: 'initiative-overview',
        component: InitiativeOverviewComponent,
        meta: { requiresAuth: true, title: 'Initiative Overview' },
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('planning.you_dont_have_permission_to_access_this_page'),
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
    }
];