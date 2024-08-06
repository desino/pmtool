import { createRouter, createWebHistory } from 'vue-router';
import store from "../store/index.js";
import { routes } from "../router/routes.js";
import NotFoundComponent from "../components/Errors/NotFoundComponent.vue";
import { APP_VARIABLES } from './../constants.js';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...routes,
        // {
        //     path: '/:catchAll(.*)',
        //     name: 'not-found',
        //     component: NotFoundComponent
        // } // Register the catch-all route
    ]
});

router.beforeEach((to, from, next) => {
    let isAuthenticated = store.getters.isAuthenticated;

    // Update document title based on route meta
    document.title = APP_VARIABLES.APP_NAME + ' | ' + to.meta.title;
    // console.log(to);
    // debugger;
    // Redirect logic based on authentication state
    if ((to.name === 'login' || to.name === 'forgot-password') && isAuthenticated) {
        next({ name: 'home' });
    } else if (!isAuthenticated && to.meta.requiresAuth) {
        next({ name: 'login' });
    } else {
        next();  // Continue navigation
    }
});


export default router;
