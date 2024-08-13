<!-- src/components/GlobalMessage.vue -->
<template>
    <div v-if="state.message" :class="['alert m-4', alertClass]">
        <button type="button" class="btn-close" aria-label="Close" @click="clearMessage"></button>
        {{ state.message }}
    </div>
</template>

<script>
import { computed } from 'vue';
import messageService from './../services/messageService';

export default {
    name: 'GlobalMessage',
    /**
     * Sets up the component by initializing the state and computed properties.
     *
     * @return {Object} An object containing the state, alertClass, and clearMessage.
     */
    setup() {
        const state = messageService.getState();

        const alertClass = computed(() => {
            return {
                'alert-danger': state.type === 'danger',
                'alert-success': state.type === 'success',
                'alert-info': state.type === 'info',
                'alert-warning': state.type === 'warning'
            };
        });

        const clearMessage = () => {
            messageService.clearMessage();
        };

        return {
            state,
            alertClass,
            clearMessage
        };
    }
};
</script>

<style scoped>
.alert {
    margin-top: 1rem;
}

.btn-close {
    position: absolute;
    right: 1rem;
}
</style>
