import store from "../store/index.js";
import router from "../router/index.js";

const axiosRequest = axios.create({
    'Content-Type': 'application/json',
});

const serverErrorNotShowStatusCode = [
    422,
    400
];

axiosRequest.interceptors.request.use(
    config => {
        store.commit('setServerError', {});
        const token = store.state.token;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    error => {
        store.commit('setLoading', false);
        store.commit('setServerError', error);
        return Promise.reject(error);
    }
);

axiosRequest.interceptors.response.use(
    response => response,
    async error => {
        if (error.response.status === 401) {
            store.commit('setAuth', false);
            store.commit('setToken', null);
            store.commit('setUser', null);
            await router.push({ name: 'login' });
        }
        if (!serverErrorNotShowStatusCode.includes(error.response.status)) {
            store.commit('setLoading', false);
            store.commit('setServerError', error);
        }
        return Promise.reject(error);
    }
);

export default axiosRequest;
