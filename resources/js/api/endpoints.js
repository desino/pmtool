const prefix = '/api';

export let endpoints = {
    auth: {
        me: `${prefix}/user`,
        login: `${prefix}/login`,
        logout: `${prefix}/logout`,
    },
    dashboard: `${prefix}/dashboard`,            
}
 