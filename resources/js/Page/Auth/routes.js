import ForgotPasswordComponent from "./ForgotPasswordComponent.vue";
import LoginComponent from "./LoginComponent.vue";
import ResetPasswordComponent from "./ResetPasswordComponent.vue";


export default [
    {
        path: '/login',
        name: 'login',
        component: LoginComponent,
        meta: {title: 'Login'}
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPasswordComponent,
        meta: {title: 'Forgot Password'}
    },
    {
        // path: '/reset-password/:token/:email',
        path: '/reset-password',
        name: 'reset-password',
        component: ResetPasswordComponent,
        meta: {title: 'Reset Password'}
    },
];