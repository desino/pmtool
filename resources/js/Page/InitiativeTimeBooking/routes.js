import store from '@/store';
import { watchEffect } from 'vue';
import i18n from '../../i18n';
import InitiativeTimeBookingsComponent from './InitiativeTimeBookingsComponent.vue';

export default [
    {
        path: '/initiative-time-booking',
        name: 'initiative-time-booking',
        component: InitiativeTimeBookingsComponent,
        meta: { requiresAuth: true, title: 'Initiative Time Booking' },
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('opportunities.you_dont_have_permission_to_access_this_page')
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