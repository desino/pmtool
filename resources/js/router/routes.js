import NotFoundComponent from "../components/Errors/NotFoundComponent.vue";
import authRoutes from "../Page/Auth/routes";
import HomeRoutes from "../Page/Home/routes";
import OpportunityRoutes from "../Page/Opportunity/routes";
import SolutionDesignRoutes from "../Page/SolutionDesign/routes";

export let routes = [
    ...authRoutes,
    ...HomeRoutes,
    ...OpportunityRoutes,
    ...SolutionDesignRoutes,
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: NotFoundComponent,
    },
]

