
import DashboardComponent from "./DashboardComponent.vue";
export default [
    {
        path: '/',
        name: 'dashboard',
        component: DashboardComponent,
        meta: {requiresAuth: true, title: 'Dashboard'}
    }
];