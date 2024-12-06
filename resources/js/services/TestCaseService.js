
import { handleServerError, handleValidationErrors } from "./ErrorService.js";
import axiosRequest from "../config/axios.js";
import { APP_VARIABLES } from "../constants.js";

const defaultPath = `${APP_VARIABLES.DEFAULT_API_PATH}/solution-design/:initiative_id/ticket/:ticket_id/test-case`;

const endpoints = {
    show: `${defaultPath}/show/:test_case_id`,
    store: `${defaultPath}/store`,
    update: `${defaultPath}/update/:test_case_id`,
    deleteTestCase: `${defaultPath}/delete-test-case/:test_case_id`,
}

const TestCaseService = {
    async getTestCase(initiative_id, ticket_id, test_case_id) {
        try {
            const endpoint = endpoints.show.replace(':initiative_id', initiative_id).replace(':ticket_id', ticket_id).replace(':test_case_id', test_case_id);
            const response = await axiosRequest.get(endpoint);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async storeTestCase(data) {
        try {
            const endpoint = endpoints.store.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },
    async updateTestCase(data) {
        try {
            const endpoint = endpoints.update.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id).replace(':test_case_id', data.test_case_id);
            const response = await axiosRequest.post(endpoint, data);
            return response.data;
        } catch (error) {
            throw handleError(error);
        }
    },

    async deleteTestCase(data) {
        try {
            const endpoint = endpoints.deleteTestCase.replace(':initiative_id', data.initiative_id).replace(':ticket_id', data.ticket_id).replace(':test_case_id', data.test_case_id);;
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
