import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../Store/index.js";
import router from "../Router/index.js";
import axiosRequest from "../Config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/header`;
const endpoints = {
    getInitiatives: `${defaultPath}/get-initiatives`,    

};
const HeaderService = {    
    async getInitiatives(status = null) {
        try {               
            // const response = await axiosRequest.get(endpoints.getInitiatives);
            const response = await axiosRequest.get('/api/header/get-initiatives');
            // return response;
        } catch (error) {
            return handleError(error);
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

export default HeaderService;