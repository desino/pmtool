import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/client`;
const endpoints = {
    store: `${defaultPath}/store`,
};

const ClientService = {
    async storeClient(data) {
        try {   
            const response = await axiosRequest.post(endpoints.store, data);  
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

export default ClientService;