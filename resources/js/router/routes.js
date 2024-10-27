import NotFoundComponent from "../components/Errors/NotFoundComponent.vue";
import authRoutes from "../Page/Auth/routes";
import HomeRoutes from "../Page/Home/routes";
import OpportunityRoutes from "../Page/Opportunity/routes";
import SolutionDesignRoutes from "../Page/SolutionDesign/routes";
import TimeBookingRoutes from "../Page/TimeBooking/routes";
import PlaningRoutes from "../Page/Planning/routes";
import InitiativeTimeBookingsRoutes from "../Page/InitiativeTimeBooking/routes";
import ProfileRoutes from "../Page/Profile/routes";

export let routes = [
    ...authRoutes,
    ...HomeRoutes,
    ...OpportunityRoutes,
    ...SolutionDesignRoutes,
    ...TimeBookingRoutes,
    ...PlaningRoutes,
    ...InitiativeTimeBookingsRoutes,
    ...ProfileRoutes,
    {
        path: '/:catchAll(.*)',
        name: 'not-found',
        component: NotFoundComponent,
    },
]

