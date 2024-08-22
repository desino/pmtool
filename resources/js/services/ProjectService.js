import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/project`;

const endpoints = {
    getProjects: `${defaultPath}`,
    changeStatus: `${defaultPath}/change-status`,
    updateProject: `${defaultPath}/update`,
}

const ProjectService = {
    async getProjects(data) {
        try {
            const endpoint = endpoints.getProjects.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

    async updateProjectStatus(data) {
        try {
            const endpoint = endpoints.changeStatus.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async updateProject(data) {
        try {
            const endpoint = endpoints.updateProject.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
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

export default ProjectService;
