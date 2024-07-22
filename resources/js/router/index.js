import { createRouter, createWebHistory } from 'vue-router';
import store from "../Store/index.js";
import {routes} from "../Router/routes.js";
import NotFoundComponent from "../Components/Errors/NotFoundComponent.vue";
import { APP_VARIABLES } from './../constants.js';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...routes,
        { path: '/:catchAll(.*)', component: NotFoundComponent } // Register the catch-all route
    ]
});

router.beforeEach((to, from, next) => {
    let isAuthenticated = store.getters.isAuthenticated ;

    // Update document title based on route meta
    if (to.meta && to.meta.title && (to.meta.requiresAuth && isAuthenticated)) {
        document.title = APP_VARIABLES.APP_NAME+' | ' + to.meta.title;
    } else {
        document.title = APP_VARIABLES.APP_NAME;
    }

    // Redirect logic based on authentication state
    if (to.name === 'login' && isAuthenticated) {
        next({ name: 'dashboard' });
    } else if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    } else {
        next();  // Continue navigation
    }
});


export default router;
 