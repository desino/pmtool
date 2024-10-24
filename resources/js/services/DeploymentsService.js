import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/deployments`;

const endpoints = {
    getDeployments: `${defaultPath}`,
    getInitiativeDataForDeployments: `${defaultPath}/get-initiative-data-for-deployments`,
    downloadReleaseNotes: `${defaultPath}/download-release-notes`,
    downloadTestResults: `${defaultPath}/download-test-results`,
}

const DeploymentService = {
    async getDeployments(data) {
        try {
            const endpoint = endpoints.getDeployments.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getInitiativeDataForDeployments(data) {
        try {
            const endpoint = endpoints.getInitiativeDataForDeployments.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async downloadReleaseNotes(data) {
        try {
            const endpoint = endpoints.downloadReleaseNotes.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data, { responseType: 'blob' });
            return response;
        } catch (error) {
            throw handleError(error);
        }
    },
    async downloadTestResults(data) {
        try {
            const endpoint = endpoints.downloadTestResults.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data, { responseType: 'blob' });
            return response;
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

export default DeploymentService;