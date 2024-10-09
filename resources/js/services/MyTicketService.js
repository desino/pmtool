import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/my-ticket`;

const endpoints = {
    getTickets: `${defaultPath}`,
}

const MyTicketService = {
    async getMyTickets(data) {
        try {
            const endpoint = endpoints.getTickets.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    }
};

function handleError(error) {
    const validationErrors = handleValidationErrors(error);
    if (validationErrors) {
        return validationErrors;
    } else {
        return handleServerError(error);
    }
}

export default MyTicketService;