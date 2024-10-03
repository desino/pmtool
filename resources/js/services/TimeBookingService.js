import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/time-booking`;
const endpoints = {
    getTimeBookingData: `${defaultPath}`,
    storeTimeBooking: `${defaultPath}/store`,
    getTimeBookingModalInitialData: `${defaultPath}/get-time-booking-modal-initial-data`,
    deleteTimeBookings: `${defaultPath}/delete-time-bookings`,
    getTimeBookingOnNewTicketModalInitialData: `${defaultPath}/get-time-booking-on-new-ticket-modal-initial-data`,
    storeTimeBookingOnNewTicket: `${defaultPath}/store-time-booking-on-new-ticket`,
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

    async storeTimeBooking(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeTimeBooking, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeTimeBookingOnNewTicket(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeTimeBookingOnNewTicket, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getTimeBookingModalInitialData(data) {
        try {
            const response = await axiosRequest.get(endpoints.getTimeBookingModalInitialData, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getTimeBookingOnNewTicketModalInitialData(data) {
        try {
            const response = await axiosRequest.get(endpoints.getTimeBookingOnNewTicketModalInitialData, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async deleteTimeBookings(data) {
        try {
            const response = await axiosRequest.post(endpoints.deleteTimeBookings, data);
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

export default TimeBookingService;