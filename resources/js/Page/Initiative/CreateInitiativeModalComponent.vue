<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="storeInitiative">
            <div class="modal-content">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title font-italic" id="createInitiativeModalLabel">{{
                        $t('create_initiative_modal_title') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{
                            $t('create_initiative_modal_select_client_name') }}
                            <strong class="text-danger">*</strong></label>
                        <select v-model="formData.client_id" :class="{ 'is-invalid': errors.client_id }"
                            class="form-select">
                            <option value="">{{ $t('create_initiative_modal_select_client_name_placeholder') }}</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}
                            </option>
                        </select>
                        <div v-if="errors.client_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.client_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $t('create_initiative_modal_input_name') }} <strong
                                class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.name" :class="{ 'is-invalid': errors.name }"
                            class="form-control">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{
                            $t('create_initiative_modal_input_ballpark_development_hours') }} <strong
                                class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.ballpark_development_hours"
                            :class="{ 'is-invalid': errors.ballpark_development_hours }" class="form-control">
                        <div v-if="errors.ballpark_development_hours" class="invalid-feedback">
                            <span v-for="(error, index) in errors.ballpark_development_hours" :key="index">
                                {{ error }} <br>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.is_sold"
                                :class="{ 'is-invalid': errors.is_sold }" type="checkbox" id="initiative_is_sold">
                            <label class="form-check-label fw-bold" for="initiative_is_sold">
                                {{ $t('create_initiative_modal_input_is_sold') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1 align-items-center">
                        <div class="col-6">
                            <button type="submit" class="btn btn-desino w-100 border-0">{{
                                $t('create_initiative_modal_submit_but_text')
                            }}</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
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
import eventBus from '../../eventBus';
import { mapActions } from 'vuex';

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
        ...mapActions(['setLoading']),
        async storeInitiative() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await InitiativeService.storeInitiative(this.formData);
                showToast(response.data.message, 'success');
                this.hideModal();
                eventBus.$emit('reloadOpportunityList');
                eventBus.$emit('appendHeaderInitiativeSelectBox', response.data.content);
                this.$router.push({ name: 'solution-design', params: { id: response.data.content.initiative.id } });
                this.setLoading(false);
            } catch (error) {
                this.handleLoginError(error);
            }
        },

        async fetchClients() {
            this.setLoading(true);
            const response = await InitiativeService.getInitiativeClients();
            this.clients = response.data.content;
            this.setLoading(false);
        },
        handleLoginError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger', 'modal');
            }
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage('modal');
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
