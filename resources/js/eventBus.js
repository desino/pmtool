// eventBus.js
import { reactive } from 'vue';

const eventBus = reactive({
    events: {}
});

const $on = (event, callback) => {
    if (!eventBus.events[event]) {
        eventBus.events[event] = [];
    }
    eventBus.events[event].push(callback);
};

const $emit = (event, data) => {
    if (eventBus.events[event]) {
        eventBus.events[event].forEach(callback => callback(data));
    }
};

const $off = (event, callback) => {
    if (eventBus.events[event]) {
        eventBus.events[event] = eventBus.events[event].filter(cb => cb !== callback);
    }
};

export default {
    $on,
    $emit,
    $off
};
