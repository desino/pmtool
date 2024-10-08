import axios from "axios";
import { handleServerError, handleValidationErrors } from "../ErrorService.js";
import store from "../../store/index.js";
import router from "../../router/index.js";
import axiosRequest from "../../config/axios.js";
import { APP_VARIABLES } from "../../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/deployment-center`;

const endpoints = {
    index: `${defaultPath}`,
    getTestDeploymentTicketsModalData: `${defaultPath}/get-test-deployment-tickets-modal-data/:initiative_id`,
    submitTestDeploymentTicket: `${defaultPath}/submit-test-deployment-ticket/:initiative_id`,
    getAcceptanceDeploymentTicketsModalData: `${defaultPath}/get-acceptance-deployment-tickets-modal-data/:initiative_id`,
    submitAcceptanceDeploymentTicket: `${defaultPath}/submit-acceptance-deployment-ticket/:initiative_id`,
    getProductionDeploymentTicketsModalData: `${defaultPath}/get-production-deployment-tickets-modal-data/:initiative_id`,
    submitProductionDeploymentTicket: `${defaultPath}/submit-production-deployment-ticket/:initiative_id`
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
    },
    async getAcceptanceDeploymentTicketsModalData(data) {
        try {
            const endpoint = endpoints.getAcceptanceDeploymentTicketsModalData.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getProductionDeploymentTicketsModalData(data) {
        try {
            const endpoint = endpoints.getProductionDeploymentTicketsModalData.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async submitTestDeploymentTicket(data) {
        try {
            const endpoint = endpoints.submitTestDeploymentTicket.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async submitAcceptanceDeploymentTicket(data) {
        try {
            const endpoint = endpoints.submitAcceptanceDeploymentTicket.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async submitProductionDeploymentTicket(data) {
        try {
            const endpoint = endpoints.submitProductionDeploymentTicket.replace(':initiative_id', data.initiative_id);
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

export default DeploymentCenterService;