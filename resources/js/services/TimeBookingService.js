import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/time-booking`;
const endpoints = {
    getTimeBookingData: `${defaultPath}`,
}

const TimeBookingService = {
    async getTimeBookingData(data) {
        try {
            const response = await axiosRequest.get(endpoints.getTimeBookingData, { params: data });
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

export default TimeBookingService;