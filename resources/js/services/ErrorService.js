/**
 * Handles validation errors by checking if the response status is 422 and returns an object with the type and errors.
 *
 * @param {Object} error - The error object.
 * @return {Object|null} An object with the type and errors if the response status is 422, otherwise null.
 */
export function handleValidationErrors(error) {
    if (error.response && error.response.status === 422) {
        return {
            type: 'validation',
            errors: error.response.data.content,
            status: error.response.status
        };
    }
    return null;
}

/**
 * Handles server errors by checking if the response status is 500 and returns an object with the type and message.
 *
 * @param {Object} error - The error object.
 * @return {Object} An object with the type and message.
 */
export function handleServerError(error) {
    if (error.response && [500, 404, 403, 401, 400].includes(error.response.status)) {
        return {
            type: 'server',
            message: error.response.data.message,
            status: error.response.status
        };
    }
    return {
        type: 'default',
        message: 'An error occurred. Please try again.',
        status: error.response.status
    };
}
