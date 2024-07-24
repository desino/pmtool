import { reactive } from "vue";

const state = reactive({
    message: "",
    type: "",
});

const messageService = {
    getState() {
        return state;
    },
    setMessage(message, type = "") {
        state.message = message;
        state.type = type;
    },
    clearMessage() {
        state.message = "";
        state.type = "";
    },
};

export default messageService;
