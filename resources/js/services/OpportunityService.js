import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../Store/index.js";
import router from "../Router/index.js";
import axiosRequest from "../Config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/opportunity`;
// const defaultPath = `${import.meta.env.VITE_API_PRIFIX}/opportunity`;
const endpoints = {
    getOpportunities: `${defaultPath}`,

};
const OpportunityService = {    
    async fetchAllOppertunites() {
        try {                                    
            console.log('dddendpoints.getOpportunitiesLL',endpoints.getOpportunities);
            const response = await axiosRequest.post('api/opportunity');
            // const response = await axiosRequest.get('/api/header/get-initiatives');
            return response.data;
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

export default OpportunityService;