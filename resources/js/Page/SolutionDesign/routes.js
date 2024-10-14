import SolutionDesignComponent from "./SolutionDesignComponent.vue";
import DetailSolutionDesignComponent from "./DetailSolutionDesignComponent.vue";
import TicketDetailDeisgnComponent from "@/Page/SolutionDesign/Ticket/TicketDetailDeisgnComponent.vue";
import TicketListComponent from "@/Page/SolutionDesign/Ticket/TicketListComponent.vue";
import ProjectListComponent from "./Project/ProjectListComponent.vue";
import DownloadSolutionDesignComponent from "./DownloadSolutionDesignComponent.vue";
import MyTicketListComponent from "./MyTicket/MyTicketListComponent.vue";
import BulkCreateTicketsComponent from "./BulkCreateTickets/BulkCreateTicketsComponent.vue";

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
        path: '/solution-design/download/:id',
        name: 'solution-design.download',
        component: DownloadSolutionDesignComponent,
        meta: { requiresAuth: true, title: 'Solution Design Download' },
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
    },
    {
        path: '/solution-design/:id/projects',
        name: 'projects',
        component: ProjectListComponent,
        meta: { requiresAuth: true, title: 'Project' },
    },
    {
        path: '/solution-design/:id/my-tickets/',
        name: 'my-tickets',
        component: MyTicketListComponent,
        meta: { requiresAuth: true, title: 'My Task List' },
    },
    {
        path: '/solution-design/:id/bulk-create-tickets/',
        name: 'bulk-create-tickets',
        component: BulkCreateTicketsComponent,
        meta: { requiresAuth: true, title: 'Bulk Create Tickets' },
    },
];
