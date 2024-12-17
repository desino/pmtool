import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/ticket/:ticket_id/comment`;

const endpoints = {
    index: `${defaultPath}`,
    store: `${defaultPath}/store`,
    delete: `${defaultPath}/delete`,
    update: `${defaultPath}/update`,
}

const CommentService = {
    async index(initiative_id, ticket_id, data) {
        try {
            const endpoint = endpoints.index.replace(':initiative_id', initiative_id).replace(':ticket_id', ticket_id);
            const response = await axiosRequest.get(endpoint, { params: data });
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async store(data) {
        try {
            const endpoint = endpoints.store.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async delete(initiative_id, ticket_id, data) {
        try {
            const endpoint = endpoints.delete.replace(':initiative_id', initiative_id).replace(':ticket_id', ticket_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async update(initiative_id, ticket_id, data) {
        try {
            const endpoint = endpoints.update.replace(':initiative_id', initiative_id).replace(':ticket_id', ticket_id);
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

export default CommentService;