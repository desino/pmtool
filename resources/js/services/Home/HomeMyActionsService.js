import axios from "axios";
import { handleServerError, handleValidationErrors } from "../ErrorService.js";
import store from "../../store/index.js";
import router from "../../router/index.js";
import axiosRequest from "../../config/axios.js";
import { APP_VARIABLES } from "../../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/home-my-actions`;

const endpoints = {
    index: `${defaultPath}`,
}

const HomeMyActionsService = {
    async getMyActionsData() {
        try {
            const response = await axiosRequest.get(endpoints.index);
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

export default HomeMyActionsService;