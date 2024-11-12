import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/all-tickets-without-initiative`;

const endpoints = {
    getInitialData: `${defaultPath}/get-initial-data`,
    getAllTicketsWithoutInitiative: `${defaultPath}`,
    addRemovePriority: `${defaultPath}/add-remove-priority`,
    markAsVisibleInvisible: `${defaultPath}/mark-as-visible-invisible`,
}

const AllTicketsWithoutInitiativeService = {
    async getInitialData() {
        try {
            const response = await axiosRequest.get(endpoints.getInitialData);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getAllTicketsWithoutInitiative(data) {
        try {
            const response = await axiosRequest.get(endpoints.getAllTicketsWithoutInitiative, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async addRemovePriority(data) {
        try {
            const response = await axiosRequest.post(endpoints.addRemovePriority, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async markAsVisibleInvisible(data) {
        try {
            const response = await axiosRequest.post(endpoints.markAsVisibleInvisible, data);
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

export default AllTicketsWithoutInitiativeService;