<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="storeInitiative">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createInitiativeModalLabel">{{ $t('create_initiative_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div class="mb-3">
                        <label for="client_id" class="form-label">{{ $t('create_initiative_modal_select_client_name') }} <strong class="text-danger">*</strong></label>
                        <select v-model="formData.client_id" :class="{'is-invalid': errors.client_id}" id="client_id" class="form-select">
                            <option value="">{{ $t('create_initiative_modal_select_client_name_placeholder') }}</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                        </select>
                        <div v-if="errors.client_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.client_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ $t('create_initiative_modal_input_name') }} <strong class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.name" :class="{'is-invalid': errors.name}" id="name" class="form-control">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ballpark_development_hours" class="form-label">{{ $t('create_initiative_modal_input_ballpark_development_hours') }} <strong class="text-danger">*</strong></label>
                        <input type="number" v-model="formData.ballpark_development_hours" :class="{'is-invalid': errors.ballpark_development_hours}" id="ballpark_development_hours" class="form-control">
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.is_sold" :class="{'is-invalid': errors.is_sold}" type="checkbox" id="is_sold">
                            <label class="form-check-label" for="is_sold">
                                {{ $t('create_initiative_modal_input_is_sold') }}
                            </label>
                        </div>
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">{{ error }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{ $t('create_initiative_modal_submit_but_text') }}</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import GlobalMessage from './../../components/GlobalMessage.vue';
    import InitiativeService from '../../services/InitiativeService';
    import messageService from '../../services/messageService';
    import { Modal } from 'bootstrap';
    import showToast from '../../utils/toasts';
    export default {
        name: 'CreateInitiativeModalComponent',
        components: {
            GlobalMessage,
        },
        data() {
            return {
                formData: {
                    client_id: '',
                    name: '',
                    ballpark_development_hours: '',
                    is_sold: false,
                },
                clients: [],
                errors: {},
                showMessage: true
            };
        },
        methods: {
            async storeInitiative() {
                this.clearMessages();
                try {
                    const response = await InitiativeService.storeInitiative(this.formData);
                    // messageService.setMessage(response.data.message, 'success');
                    showToast(response.data.message, 'success');
                    this.hideModal();
                } catch (error) {
                    this.handleLoginError(error);
                }
            },

            async fetchClients(){
                const response = await InitiativeService.getInitiativeClients();
                this.clients = response.data.content;
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
                const modalElement = document.getElementById('createInitiativeModal');
                if (modalElement) {
                    const modal = Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            },
            resetForm() {
                this.formData = {
                    client_id: '',
                    name: '',
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
    }
</script>