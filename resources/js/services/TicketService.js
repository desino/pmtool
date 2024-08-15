import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/ticket`;

const endpoints = {
    getSectionFunctionalityForCreateTicketModal: `${defaultPath}/get-section-functionality`,
    storeTicket: `${defaultPath}/store`,
    fetchTicket: `${defaultPath}/show/:id`,
    updateReleaseNote: `${defaultPath}/update-release-note/:id`,
    fetchAllTicketForDropDown : `${defaultPath}/all-tickets`
}

const SolutionDesignService = {
    async getInitiativeSectionFunctionality(data) {
        try {
            const response = await axiosRequest.post(endpoints.getSectionFunctionalityForCreateTicketModal, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeTicket(data) {
        try {
            const response = await axiosRequest.post(endpoints.storeTicket, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async updateReleaseNote(id,data){
        try {
            const endpoint = endpoints.updateReleaseNote.replace(':id', id);
            const response = await axiosRequest.post(endpoint,data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchTicket(id){
        try {
            const endpoint = endpoints.fetchTicket.replace(':id', id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchAllTicketForDropDown(){
        try {
            const endpoint = endpoints.fetchAllTicketForDropDown;
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

export default SolutionDesignService;
