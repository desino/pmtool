import LoginComponent from "../components/LoginComponent.vue";
import DashboardComponent from "../components/DashboardComponent.vue";


export let routes = [
    {
        path: '/login',
        name: 'login',
        component: LoginComponent,
        meta: {title: 'Login'}
    },
    {
        path: '/',
        name: 'dashboard',
        component: DashboardComponent,
        meta: {requiresAuth: true, title: 'Dashboard'}
    }    
]

 