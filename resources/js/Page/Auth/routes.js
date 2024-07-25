import ForgotPasswordComponent from "./ForgotPasswordComponent.vue";
import LoginComponent from "./LoginComponent.vue";
import Office365LoginCallbackComponent from "./Office365LoginCallbackComponent.vue";
import Office365LoginComponent from "./Office365LoginComponent.vue";
import ResetPasswordComponent from "./ResetPasswordComponent.vue";


export default [
    {
        path: '/login',
        name: 'login',
        // component: LoginComponent,
        component: Office365LoginComponent,
        meta: {title: 'Login'}
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPasswordComponent,
        meta: {title: 'Forgot Password'}
    },
    {
        path: '/reset-password/:token/:email',
        name: 'reset-password',
        component: ResetPasswordComponent,
        meta: {title: 'Reset Password'}
    },
    {
        path: '/office-365-login/graph/callback',
        name: 'office-365-login-callback',
        component: Office365LoginCallbackComponent,
        meta: {title: 'Office 365 login callback'}
    },
];