import { watchEffect } from "vue";
import store from "../../store";
import i18n from "../../i18n";
import ActivityLogsComponent from "./ActivityLogsComponent.vue";

export default [
    {
        path: '/activity-logs',
        name: 'activity-logs',
        component: ActivityLogsComponent,
        meta: { requiresAuth: true, title: 'Activity Logs' },
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