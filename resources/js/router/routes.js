import NotFoundComponent from "../components/Errors/NotFoundComponent.vue";
import authRoutes from "../Page/Auth/routes";
import DashboardRoutes from "../Page/Dashboard/routes";
import OpportunityRoutes from "../Page/Opportunity/routes";
import SolutionDesignRoutes from "../Page/SolutionDesign/routes";

export let routes = [
    ...authRoutes,
    ...DashboardRoutes,
    ...OpportunityRoutes,
    ...SolutionDesignRoutes,
    {
        path: '/:catchAll(.*)',
        component: NotFoundComponent,
    },
]

