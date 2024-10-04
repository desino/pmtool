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
    getTimeBookingOnNewInitiativeOrTicketModalInitialData: `${defaultPath}/get-time-booking-on-new-initiative-or-ticket-modal-initial-data`,
    storeTimeBookingOnNewInitiativeOrTicket: `${defaultPath}/store-time-booking-on-new-initiative-or-ticket`,
    fetchTickets: `${defaultPath}/fetch-tickets`,
    storeTimeBookingForTicketDetail: `${defaultPath}/store-time-booking-for-ticket-detail`,
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
    async storeTimeBookingForTicketDetail(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeTimeBookingForTicketDetail, data);
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
    async storeTimeBookingOnNewInitiativeOrTicket(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeTimeBookingOnNewInitiativeOrTicket, data);
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
    async getTimeBookingOnNewInitiativeOrTicketModalInitialData(data) {
        try {
            const response = await axiosRequest.get(endpoints.getTimeBookingOnNewInitiativeOrTicketModalInitialData, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchTickets(data) {
        try {
            const response = await axiosRequest.get(endpoints.fetchTickets, { params: data });
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