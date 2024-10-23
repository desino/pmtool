import { createRouter, createWebHistory } from 'vue-router';
import store from "../store/index.js";
import { routes } from "../router/routes.js";
import NotFoundComponent from "../components/Errors/NotFoundComponent.vue";
import { APP_VARIABLES } from './../constants.js';
import eventBus from "./../eventBus.js";
import OpportunityService from '../services/OpportunityService.js';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...routes,
    ]
});

router.beforeEach((to, from, next) => {
    let isAuthenticated = store.getters.isAuthenticated;

    const routesWithInitiativeId = [
        'solution-design',
        'solution-design.detail',
        'solution-design.download',
        'tasks',
        'task.detail',
        'projects',
        'my-tickets',
        'bulk-create-tickets',
        'deployments',
    ];

    if (routesWithInitiativeId.includes(to.name)) {
        let initiativeId = to.params.initiative_id ?? to.params.id;

        OpportunityService.getOpportunity(initiativeId)
            .then(response => {
                if (response && response.content) {
                    store.commit("setCurrentInitiative", response.content);
                }
            });
    }

    // Update document title based on route meta
    document.title = APP_VARIABLES.APP_NAME + ' | ' + to.meta.title;

    store.commit('setServerError', {});

    // Redirect logic based on authentication state
    if ((to.name === 'login' || to.name === 'forgot-password') && isAuthenticated) {
        next({ name: 'home' });
    } else if (!isAuthenticated && to.meta.requiresAuth) {
        next({ name: 'login' });
    } else {
        next();  // Continue navigation
    }
});


function test() {

}
export default router;
