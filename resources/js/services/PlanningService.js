import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/planning`;
const endpoints = {
    getPlanningData: `${defaultPath}`,
    getPlanningInitialData: `${defaultPath}/get-planning-initial-data`,
    storePlanning: `${defaultPath}/store-planning`,
    fetchProjects: `${defaultPath}/fetch-projects`,
}

const PlanningService = {
    async getPlanningInitialData() {
        try {
            const response = await axiosRequest.get(endpoints.getPlanningInitialData);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getPlanningData(data) {
        try {
            const response = await axiosRequest.get(endpoints.getPlanningData, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storePlanning(data) {
        try {
            const response = await axiosRequest.post(endpoints.storePlanning, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchProjects(data) {
        try {
            const response = await axiosRequest.get(endpoints.fetchProjects, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
}

function handleError(error) {
    const validationErrors = handleValidationErrors(error);
    if (validationErrors) {
        return validationErrors;
    } else {
        return handleServerError(error);
    }
}

export default PlanningService;