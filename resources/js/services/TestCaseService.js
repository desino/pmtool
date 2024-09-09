
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/ticket/:ticket_id/test-case`;

const endpoints = {
    show: `${defaultPath}/show/:test_case_id`,
    store: `${defaultPath}/store`,
    update: `${defaultPath}/update/:test_case_id`,
}

const TestCaseService = {
    async getTestCase(ticket_id,test_case_id) {
        try {
            const endpoint = endpoints.show.replace(':ticket_id', ticket_id).replace(':test_case_id', test_case_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeTestCase(data) {
        try {
            const endpoint = endpoints.store.replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async updateTestCase(data) {
        try {
            const endpoint = endpoints.update.replace(':ticket_id', data.ticket_id).replace(':test_case_id', data.test_case_id);
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

export default TestCaseService;
