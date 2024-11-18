import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/time-mapping`;

const endpoints = {
    getTimeMapping: `${defaultPath}`,
    getInitialDataForTimeMappings: `${defaultPath}/get-initial-data-for-time-mappings`,
    getProjectListForTimeMappings: `${defaultPath}/get-project-list-for-time-mappings`,
    assignProjectForTimeMappings: `${defaultPath}/assign-project-for-time-mappings`,
}

const TimeMappingService = {
    async getTimeMappings(data) {
        try {
            const response = await axiosRequest.get(endpoints.getTimeMapping, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getInitialDataForTimeMappings() {
        try {
            const response = await axiosRequest.get(endpoints.getInitialDataForTimeMappings);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getProjectListForTimeMappings(data) {
        try {
            const response = await axiosRequest.get(endpoints.getProjectListForTimeMappings, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async assignProjectForTimeMappings(data) {
        try {
            const response = await axiosRequest.post(endpoints.assignProjectForTimeMappings, data);
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

export default TimeMappingService;