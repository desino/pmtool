export const APP_VARIABLES = {
    APP_NAME: import.meta.env.VITE_APP_NAME,
    DEFAULT_API_PATH: import.meta.env.VITE_API_PRIFIX,
    ENABLE_MANUAL_LOGIN: import.meta.env.VITE_ENABLE_MANUAL_LOGIN === 'true' ? true : false,
    ENABLE_OFFICE_365_LOGIN: import.meta.env.VITE_ENABLE_OFFICE_365_LOGIN === 'true' ? true : false,
};
