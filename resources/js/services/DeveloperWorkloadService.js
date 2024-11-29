import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/developer-workload`;
const endpoints = {
    getDeveloperWorkloads: `${defaultPath}`,
    getDeveloperWorkloadTicketModalData: `${defaultPath}/get-developer-workload-ticket-modal-data`,
};

const DeveloperWorkloadService = {

    async getDeveloperWorkloads() {
        try {
            const response = await axiosRequest.get(endpoints.getDeveloperWorkloads);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getDeveloperWorkloadTicketModalData(data) {
        try {
            const response = await axiosRequest.get(endpoints.getDeveloperWorkloadTicketModalData, { params: data });
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

export default DeveloperWorkloadService;