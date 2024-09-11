import axios from "axios";
import { handleServerError, handleValidationErrors } from "../ErrorService.js";
import store from "../../store/index.js";
import router from "../../router/index.js";
import axiosRequest from "../../config/axios.js";
import { APP_VARIABLES } from "../../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/deployment-center`;

const endpoints = {
    index: `${defaultPath}/`,
    getTestDeploymentTicketsModalData: `${defaultPath}/get-test-deployment-tickets-modal-data/:initiative_id`,
}

const DeploymentCenterService = {
    async getDeploymentCenterData() {
        try {
            const response = await axiosRequest.get(endpoints.index);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getTestDeploymentTicketsModalData(data) {
        try {
            const endpoint = endpoints.getTestDeploymentTicketsModalData.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint);
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

export default DeploymentCenterService;