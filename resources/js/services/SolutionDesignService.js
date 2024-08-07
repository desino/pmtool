import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design`;

const endpoints = {
    getSectionsWithFunctionalities: `${defaultPath}`,
    getInitiative: `${defaultPath}/get-initiative`,
    storeSection: `${defaultPath}/store-section`,
    storeUpdateFunctionality: `${defaultPath}/store-update-functionality`,
    deleteFunctionality: `${defaultPath}/delete-functionality`,
}

const SolutionDesignService = {
    async getInitiativeData(credentials) {
        try {
            const response = await axiosRequest.post(endpoints.getInitiative, credentials);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

    async storeSection(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeSection, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getSectionsWithFunctionalities(data) {
        try {
            const response = await axiosRequest.post(endpoints.getSectionsWithFunctionalities, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeUpdateFunctionality(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeUpdateFunctionality, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async deleteFunctionality(data) {
        try {
            const response = await axiosRequest.post(endpoints.deleteFunctionality, data);
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

export default SolutionDesignService;