import store from '@/store';
import { watchEffect } from 'vue';
import i18n from '../../i18n';
import TimeMappingComponent from './TimeMappingComponent.vue';

export default [
    {
        path: '/time-mapping',
        name: 'time-mapping',
        component: TimeMappingComponent,
        meta: { requiresAuth: true, title: 'Time Mapping' },
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('time_mapping.you_dont_have_permission_to_access_this_page')
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