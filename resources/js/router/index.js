import { createRouter, createWebHistory } from 'vue-router';
import store from "../store/index.js";
import {routes} from "../router/routes.js";
import NotFoundComponent from "../components/Errors/NotFoundComponent.vue";

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
        document.title = 'PM Tool | ' + to.meta.title;
    } else {
        document.title = 'PM Tool';
    }

    // Redirect logic based on authentication state
    if (to.name === 'admin.login' && isAuthenticated) {
        next({ name: 'admin.dashboard' });
    } else if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'admin.login' });
    } else {
        next();  // Continue navigation
    }
});


export default router;
 