import SolutionDesignComponent from "./SolutionDesignComponent.vue";
import DetailSolutionDesignComponent from "./DetailSolutionDesignComponent.vue";
import TicketDetailDeisgnComponent from "@/Page/SolutionDesign/Ticket/TicketDetailDeisgnComponent.vue";
import TicketListComponent from "@/Page/SolutionDesign/Ticket/TicketListComponent.vue";

export default [
    {
        path: '/solution-design/:id',
        name: 'solution-design',
        component: SolutionDesignComponent,
        meta: { requiresAuth: true, title: 'Solution Design' },
    },
    {
        path: '/solution-design/detail/:id',
        name: 'solution-design.detail',
        component: DetailSolutionDesignComponent,
        meta: { requiresAuth: true, title: 'Solution Design Detail' },
    },
    {
        path: '/solution-design/:id/tickets/',
        name: 'tasks',
        component: TicketListComponent,
        meta: { requiresAuth: true, title: 'Task List' },
    },
    {
        path: '/solution-design/:initiative_id/ticket-detail/:ticket_id',
        name: 'task.detail',
        component: TicketDetailDeisgnComponent,
        meta: { requiresAuth: true, title: 'Task Detail' },
    }
];
