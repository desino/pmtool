import NotFoundComponent from "../Components/Errors/NotFoundComponent.vue";
import authRoutes from "../Page/Auth/routes";
import DashboardRoutes from "../Page/Dashboard/routes";

export let routes = [
    ...authRoutes,
    ...DashboardRoutes,       
    {
        path: '/:catchAll(.*)',
        component: NotFoundComponent,
    },
]

 