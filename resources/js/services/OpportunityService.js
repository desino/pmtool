import axios from "axios";
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import store from "../store/index.js";
import router from "../router/index.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/opportunity`;
// const defaultPath = `${import.meta.env.VITE_API_PRIFIX}/opportunity`;
const endpoints = {
    getOpportunities: `${defaultPath}`,
    getInitialData: `${defaultPath}/get-initial-data`,
    updateOpportunity: `${defaultPath}/update`,
    updateStatusLost: `${defaultPath}/update-status-lost`,

};
const OpportunityService = {
    async getPageInitialData() {
        try {            
            const response = await axiosRequest.post(endpoints.getInitialData);            
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async fetchAllOpportunites(params) {
        try {
            // const response = await axiosRequest.post(endpoints.getOpportunities,params);
            const response = await axiosRequest.post(endpoints.getOpportunities,params);
            // const response = await axiosRequest.get('api/opportunity');
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

    async updateOpportunity(data){
        try {
            const response = await axiosRequest.post(endpoints.updateOpportunity,data);
            return response;
        } catch (error) {            
            throw handleError(error);
        }
    },

    async updateStatusLost(data){        
        try {
            const response = await axiosRequest.post(endpoints.updateStatusLost,data);
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

export default OpportunityService;