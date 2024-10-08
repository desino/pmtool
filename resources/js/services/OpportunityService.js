import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/opportunity`;
const endpoints = {
    getOpportunities: `${defaultPath}`,
    getInitialData: `${defaultPath}/get-initial-data`,
    updateOpportunity: `${defaultPath}/update`,
    updateStatusLost: `${defaultPath}/update-status-lost`,
    getClientList: `${defaultPath}/get-client-list`,
    getUserList: `${defaultPath}/get-user-list`,
    getOpportunity: `${defaultPath}/get-opportunity/:id`,
    getEditOpportunityData: `${defaultPath}/get-edit-opportunity-data`,

};
const OpportunityService = {
    async getPageInitialData() {
        try {
            const response = await axiosRequest.get(endpoints.getInitialData);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchAllOpportunities(params) {
        try {
            const response = await axiosRequest.post(endpoints.getOpportunities, params);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

    async updateOpportunity(data) {
        try {
            const response = await axiosRequest.post(endpoints.updateOpportunity, data);
            return response;
        } catch (error) {
            throw handleError(error);
        }
    },

    async updateStatusLost(data) {
        try {
            const response = await axiosRequest.post(endpoints.updateStatusLost, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getClientList() {
        try {
            const response = await axiosRequest.get(endpoints.getClientList);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getEditOpportunityData() {
        try {
            const response = await axiosRequest.get(endpoints.getEditOpportunityData);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getOpportunity(id) {
        try {
            const response = await axiosRequest.get(endpoints.getOpportunity.replace(':id', id));
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    }
}

function handleError(error) {
    const validationErrors = handleValidationErrors(error);
    if (validationErrors) {
        return validationErrors;
    } else {
        return handleServerError(error);
    }
}

export default OpportunityService;
