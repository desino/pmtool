import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/ticket`;

const endpoints = {
    getAllTickets: `${defaultPath}/all`,
    storeTicket: `${defaultPath}/store`,
    fetchTicket: `${defaultPath}/show/:ticket_id`,
    updateReleaseNote: `${defaultPath}/update-release-note/:ticket_id`,
    fetchAllTicketForDropDown: `${defaultPath}/all-ticket`,
    getInitiativeProjectList: `${defaultPath}/get-initiative-project-list`,
    assignProject: `${defaultPath}/assign-project`,
    assignOrRemoveProjectForTask: `${defaultPath}/assign-or-remove-project-for-task`,
    getInitialDataForCreateOrEditTicket: `${defaultPath}/get-initial-data-for-create-or-edit-ticket`,
    editTicket: `${defaultPath}/edit-ticket/:ticket_id`,
    updateTicket: `${defaultPath}/update-ticket/:ticket_id`,
    changeActionUser: `${defaultPath}/change-action-user/:ticket_id`,
    changeActionStatus: `${defaultPath}/change-action-status/:ticket_id`,
}

const SolutionDesignService = {
    async fetchAllTickets(initiative_id, data) {
        try {
            const endpoint = endpoints.getAllTickets.replace(':initiative_id', initiative_id);
            const response = await axiosRequest.get(endpoint, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeTicket(data) {
        try {
            const endpoint = endpoints.storeTicket.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async updateReleaseNote(data) {
        try {
            const endpoint = endpoints.updateReleaseNote.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchTicket(data) {
        try {
            const endpoint = endpoints.fetchTicket.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchAllTicketForDropDown(initiative_id) {
        try {
            const endpoint = endpoints.fetchAllTicketForDropDown.replace(':initiative_id', initiative_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getInitiativeProjects(initiative_id) {
        try {
            const endpoint = endpoints.getInitiativeProjectList.replace(':initiative_id', initiative_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async assignProject(data) {
        try {
            const endpoint = endpoints.assignProject.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async assignOrRemoveProjectForTask(data) {
        try {
            const endpoint = endpoints.assignOrRemoveProjectForTask.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

    async getInitialDataForCreateOrEditTicket(data) {
        try {
            const endpoint = endpoints.getInitialDataForCreateOrEditTicket.replace(':initiative_id', data.initiative_id);
            const response = await axiosRequest.get(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async getEditTicket(data) {
        try {
            const endpoint = endpoints.editTicket.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.id);
            const response = await axiosRequest.get(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async updateTicket(data) {
        try {
            const endpoint = endpoints.updateTicket.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async changeActionUser(data) {
        try {
            const endpoint = endpoints.changeActionUser.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async changeActionStatus(data) {
        try {
            const endpoint = endpoints.changeActionStatus.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.post(endpoint, data);
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

export default SolutionDesignService;
