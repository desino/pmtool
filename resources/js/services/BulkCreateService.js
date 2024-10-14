import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/bulk-create-tickets`;

const endpoints = {
    getInitialDataForBulkCreate: `${defaultPath}`,
    storeNewBulkTickets: `${defaultPath}/store-new-bulk-tickets`,
}

const BulkCreateService = {
    async getInitialDataForBulkCreate(data) {
        try {
            const endpoint = endpoints.getInitialDataForBulkCreate.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeNewBulkTickets(data) {
        try {
            const endpoint = endpoints.storeNewBulkTickets.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response;
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

export default BulkCreateService;