import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../Store/index.js";
import router from "../Router/index.js";
import axiosRequest from "../Config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/client`;
const endpoints = {
    store: `${defultPath}/store`,
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