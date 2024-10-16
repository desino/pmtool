import OpportunityListComponent from './OpportunityListcomponent.vue';
import store from '@/store';
import { watchEffect } from 'vue';
import i18n from '../../i18n';

export default [
    {
        path: '/opportunities',
        name: 'opportunities',
        component: OpportunityListComponent,
        meta: { requiresAuth: true, title: 'Opportunities' },
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