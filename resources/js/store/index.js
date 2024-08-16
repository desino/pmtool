import Vuex from 'vuex';

export default new Vuex.Store({
    state: {
        isAuthenticated: localStorage.getItem('isAuthenticated') === 'true',
        token: localStorage.getItem('token') || null,
        user: null,
        loading: false,
        currentInitiative:{}
    },
    mutations: {
        /**
         * Set the authentication status in the state and local storage.
         *
         * @param {Object} state - The Vuex state object.
         * @param {any} isAuthenticated - The authentication status to set.
         */
        setAuth(state, isAuthenticated) {
            state.isAuthenticated = isAuthenticated;
            localStorage.setItem('isAuthenticated', isAuthenticated);
        },
        /**
         * Set the token in the state and local storage.
         *
         * @param {Object} state - The Vuex state object.
         * @param {string} token - The token to set.
         */
        setToken(state, token) {
            state.token = token;
            localStorage.setItem('token', token);
        },
        /**
         * Set the user in the state.
         *
         * @param {Object} state - The Vuex state object.
         * @param {Object} user - The user object to set.
         */
        setUser(state, user) {
            state.user = user;
        },
        /**
         * Sets the loading status in the state.
         *
         * @param {Object} state - The Vuex state object.
         * @param {boolean} status - The loading status to set.
         */
        setLoading(state, status) {
            state.loading = status;
        },

        /**
         * Sets the current project/initiative in the state.
         *
         * @param {Object} state - The Vuex state object.
         * @param {Object} initiative - The initiative to set.
         */
        setCurrentInitiative(state,initiative){
            state.currentInitiative=initiative;
        }
    },
    actions: {
        setLoading({ commit }, status) {
            commit('setLoading', status);
        },
        setCurrentInitiative({commit},initiative){
            commit('setCurrentInitiative', initiative);
        }
    },
    getters: {
        token: state => state.token,
        isAuthenticated: state => state.isAuthenticated,
        user: state => state.user,
        loading: state => state.loading,
        currentInitiative: state=>state.currentInitiative
    },
});

