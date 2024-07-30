import NotFoundComponent from "../Components/Errors/NotFoundComponent.vue";
import authRoutes from "../Page/Auth/routes";
import DashboardRoutes from "../Page/Dashboard/routes";
import OpportunityRoutes from "../Page/Opportunity/routes";

export let routes = [
    ...authRoutes,
    ...DashboardRoutes,
    ...OpportunityRoutes,
    {
        path: '/:catchAll(.*)',
        component: NotFoundComponent,
    },
]

