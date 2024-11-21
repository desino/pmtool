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
    props: {
        scope: {
            type: String,
            default: "default",
        },
    },
    setup(props) {
        const state = messageService.getState(props.scope);

        const alertClass = computed(() => {
            return {
                'alert-danger': state.type === 'danger',
                'alert-success': state.type === 'success',
                'alert-info': state.type === 'info',
                'alert-warning': state.type === 'warning'
            };
        });

        const clearMessage = () => {
            messageService.clearMessage(props.scope);
        };

        return {
            state,
            alertClass,
            clearMessage,
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
