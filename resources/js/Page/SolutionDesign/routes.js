import SolutionDesignComponent from "./SolutionDesignComponent.vue";
import DetailSolutionDesignComponent from "./DetailSolutionDesignComponent.vue";
import TicketDetailDeisgnComponent from "@/Page/SolutionDesign/Ticket/TicketDetailDeisgnComponent.vue";
import TicketListComponent from "@/Page/SolutionDesign/Ticket/TicketListComponent.vue";
import ProjectListComponent from "./Project/ProjectListComponent.vue";
import DownloadSolutionDesignComponent from "./DownloadSolutionDesignComponent.vue";
import MyTicketListComponent from "./MyTicket/MyTicketListComponent.vue";
import BulkCreateTicketsComponent from "./BulkCreateTickets/BulkCreateTicketsComponent.vue";
import { watchEffect } from "vue";
import store from "../../store";
import i18n from "../../i18n";
import DeploymentsComponent from "./Deployments/DeploymentsComponent.vue";

export default [
    {
        path: '/solution-design/:id',
        name: 'solution-design',
        component: SolutionDesignComponent,
        meta: { requiresAuth: true, title: 'Solution Design' },
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('solution_design.you_dont_have_permission_to_access_this_page')
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
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
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                const currentInitiative = store.getters.currentInitiative;
                if (loggedInUser && Object.keys(currentInitiative).length > 0) {
                    if (!loggedInUser?.is_admin && currentInitiative?.functional_owner_id != loggedInUser?.id && currentInitiative.technical_owner_id != loggedInUser?.id) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('solution_design.you_dont_have_permission_to_access_this_page')
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
    },
    {
        path: '/solution-design/:id/tickets/',
        name: 'tasks',
        component: TicketListComponent,
        meta: { requiresAuth: true, title: 'Task List' },
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('solution_design.you_dont_have_permission_to_access_this_page')
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
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
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('solution_design.you_dont_have_permission_to_access_this_page')
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
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
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                if (loggedInUser) {
                    if (!loggedInUser?.is_admin) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('solution_design.you_dont_have_permission_to_access_this_page')
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
    },
    {
        path: '/solution-design/:id/deployments/',
        name: 'deployments',
        component: DeploymentsComponent,
        meta: { requiresAuth: true, title: 'Deployments' },
        beforeEnter: (to, from, next) => {
            watchEffect(() => {
                const loggedInUser = store.getters.user;
                const currentInitiative = store.getters.currentInitiative;
                if (loggedInUser && Object.keys(currentInitiative).length > 0) {
                    if (!loggedInUser?.is_admin && currentInitiative.technical_owner_id != loggedInUser?.id) {
                        const passedData = {
                            'type': 'danger',
                            'message': i18n.global.t('solution_design.you_dont_have_permission_to_access_this_page')
                        };
                        store.commit("setPermissionMessage", passedData);
                        next({ name: 'home' });
                    } else {
                        next();
                    }
                }
            });
        }
    },
];
