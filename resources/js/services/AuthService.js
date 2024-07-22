import axios from 'axios';
import {endpoints} from '../api/endpoints.js';
import {handleServerError, handleValidationErrors} from './ErrorService.js';
import store from "../Store/index.js";
import router from "../Router/index.js";
import axiosRequest from "../Config/axios.js";

const AuthService = {
    /**
     * Asynchronously logs in a user with the provided credentials.
     *
     * @param {Object} credentials - An object containing the user's login credentials.
     * @param {string} credentials.email - The user's email address.
     * @param {string} credentials.password - The user's password.
     * @return {Promise<void>} A Promise that resolves when the user is successfully logged in.
     * @throws {Error} If there is an error during the login process.
     */
    async loginUser(credentials) {
        try {
            const response = await axiosRequest.post(endpoints.auth.login, credentials);
            store.commit('setAuth', true);
            store.commit('setToken', response.data.content.token);
            store.commit('setUser', response.data.content);
        } catch (error) {
            throw handleError(error);
        }
    },

    /**
     * Asynchronously refreshes the user's information by making a GET request to the server.
     * If the request is successful, it updates the user's information in the store.
     * If there is an error, it logs the user out by setting the authentication state to false,
     * clearing the token and user information, and redirecting the user to the login page.
     *
     * @return {Promise<void>} A Promise that resolves when the user's information is updated
     * or when the user is logged out.
     */
    async refreshUser() {
        try {
            const response = await axiosRequest.get(endpoints.auth.me);
            store.commit('setUser', response.data.content);
        } catch (error) {
             store.commit('setAuth', false);
             store.commit('setToken', null);
             store.commit('setUser', null);
            await router.push({name: 'login'});
        }
    },

    /**
     * Asynchronously logs the user out by making a GET request to the server's logout endpoint.
     * If the request is successful, it updates the authentication state in the store to false,
     * clears the token and user information, and redirects the user to the login page.
     * If there is an error, it logs the error to the console.
     *
     * @return {Promise<void>} A Promise that resolves when the user is logged out.
     */
    async logout() {
        try {
            await axios.get(endpoints.auth.logout);
        } catch (error) {
            console.error('Logout error:', error);
        }
        store.commit('setAuth', false);
        store.commit('setToken', null);
        store.commit('setUser', null);
        await router.push({name: 'login'});
    },

    /**
     * Asynchronously calls dashboard api by making a GET request to the server's endpoint.
     *
     * @return {Promise<void>} A Promise that resolves when the data is fetched.
     */
    async dashboard() {
        try {
            const response = await axiosRequest.get(endpoints.dashboard);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

};

function handleError(error) {
    const validationErrors = handleValidationErrors(error);
    if (validationErrors) {
        return validationErrors;
    } else {
        return handleServerError(error);
    }
}

export default AuthService;
 