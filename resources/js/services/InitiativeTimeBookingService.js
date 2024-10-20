import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/initiative-time-booking`;

const endpoints = {
    getInitiativeTimeBooking: `${defaultPath}`,
    getInitialDataForInitiativeTimeBookings: `${defaultPath}/get-initial-data-for-initiative-time-bookings`,
    getProjectListForInitiativeTimeBookings: `${defaultPath}/get-project-list-for-initiative-time-bookings`,
}

const InitiativeTimeBookingService = {
    async getInitiativeTimeBookings(data) {
        try {
            const response = await axiosRequest.get(endpoints.getInitiativeTimeBooking, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getInitialDataForInitiativeTimeBookings() {
        try {
            const response = await axiosRequest.get(endpoints.getInitialDataForInitiativeTimeBookings);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getProjectListForInitiativeTimeBookings(data) {
        try {
            const response = await axiosRequest.get(endpoints.getProjectListForInitiativeTimeBookings, { params: data });
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

export default InitiativeTimeBookingService;