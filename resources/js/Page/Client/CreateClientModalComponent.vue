<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="storeClient">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createClientModalLabel">{{ $t('create_client_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ $t('create_client_modal_input_name') }} <strong
                                class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.name" :class="{ 'is-invalid': errors.name }" id="name"
                            class="form-control">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="initiative_name" class="form-label">{{
                            $t('create_client_modal_input_initiative_name') }} <strong
                                class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.initiative_name"
                            :class="{ 'is-invalid': errors.initiative_name }" id="initiative_name" class="form-control">
                        <div v-if="errors.initiative_name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.initiative_name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ballpark_development_hours" class="form-label">{{
                            $t('create_client_modal_input_ballpark_development_hours') }} <strong
                                class="text-danger">*</strong></label>
                        <input type="number" v-model="formData.ballpark_development_hours"
                            :class="{ 'is-invalid': errors.ballpark_development_hours }" id="ballpark_development_hours"
                            class="form-control">
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{ error
                                }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.is_sold"
                                :class="{ 'is-invalid': errors.is_sold }" type="checkbox" id="is_sold">
                            <label class="form-check-label" for="is_sold">
                                {{ $t('create_client_modal_input_is_sold') }}
                            </label>
                        </div>
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{ error
                                }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-desino">{{ $t('create_client_modal_submit_but_text')
                        }}</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import GlobalMessage from './../../components/GlobalMessage.vue';
import ClientService from '../../services/ClientService';
import messageService from '../../services/messageService';
import { Modal } from 'bootstrap';
import showToast from '../../utils/toasts';
import eventBus from './../../eventBus';
export default {
    name: 'CreateClientModalComponent',
    components: {
        GlobalMessage,
    },
    data() {
        return {
            formData: {
                name: '',
                initiative_name: '',
                ballpark_development_hours: '',
                is_sold: false,
            },
            errors: {},
            showMessage: true
        };
    },
    methods: {
        async storeClient() {
            this.clearMessages();
            try {
                const response = await ClientService.storeClient(this.formData);
                // messageService.setMessage(response.data.message, 'success');
                showToast(response.data.message, 'success');
                this.hideModal();
                eventBus.$emit('reloadOpportunityList');
                eventBus.$emit('appendHeaderInitiativeSelectBox', response.data.content);
                this.$router.push({ name: 'solution-design', params: { id: response.data.content.initiative.id } });
            } catch (error) {
                this.handleLoginError(error);
            }
        },
        handleLoginError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        hideModal() {
            const modalElement = document.getElementById('createClientModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        },
        resetForm() {
            this.formData = {
                name: '',
                initiative_name: '',
                ballpark_development_hours: '',
                is_sold: false,
            };
            this.errors = {};
        }
    },
    mounted() {
        this.clearMessages();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
};
</script>
