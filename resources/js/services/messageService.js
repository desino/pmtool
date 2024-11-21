// import { reactive } from "vue";

// const state = reactive({
//     message: "",
//     type: "",
// });

// const messageService = {
//     getState() {
//         return state;
//     },
//     setMessage(message, type = "") {
//         state.message = message;
//         state.type = type;
//     },
//     clearMessage() {
//         state.message = "";
//         state.type = "";
//     },
// };

// export default messageService;

import { reactive } from "vue";

const states = reactive({});

const DEFAULT_SCOPE = "default";

const messageService = {
    /**
     * Get the state for a specific scope. If it doesn't exist, initialize it.
     * @param {string} scope - The scope identifier (e.g., 'main', 'modal').
     * @returns {Object} - The reactive state object for the scope.
     */
    getState(scope = DEFAULT_SCOPE) {
        if (!states[scope]) {
            states[scope] = { message: "", type: "" };
        }
        return states[scope];
    },

    /**
     * Set a message and its type for a specific scope.
     * @param {string} scope - The scope identifier.
     * @param {string} message - The message text.
     * @param {string} type - The type of message (e.g., 'success', 'danger').
     */
    setMessage(message, type = "", scope = DEFAULT_SCOPE) {
        if (!states[scope]) {
            states[scope] = { message: "", type: "" };
        }
        states[scope].message = message;
        states[scope].type = type;
    },

    /**
     * Clear the message for a specific scope.
     * @param {string} scope - The scope identifier.
     */
    clearMessage(scope = DEFAULT_SCOPE) {
        if (states[scope]) {
            states[scope].message = "";
            states[scope].type = "";
        }
    },
};

export default messageService;

