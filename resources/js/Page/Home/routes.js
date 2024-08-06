
import DashboardComponent from "./HomeComponent.vue";
export default [
    {
        path: '/',
        name: 'home',
        component: DashboardComponent,
        meta: { requiresAuth: true, title: 'Home' }
    }
];
