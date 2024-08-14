<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="storeTicket">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">{{ $t('create_ticket_modal_title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <input type="hidden" name="initiative_id" v-model="formData.initiative_id">
                    <input type="hidden" name="type" v-model="formData.type">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ $t('create_ticket_modal_input_name') }} <strong
                                class="text-danger">*</strong></label>
                        <input type="text" v-model="formData.name" :class="{ 'is-invalid': errors.name }" id="name"
                            class="form-control">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="client_id" class="form-label">{{
                            $t('create_ticket_modal_select_functinoality_id') }}
                            <strong class="text-danger">*</strong></label>
                        <multiselect v-model="formData.functionality_id"
                            :class="{ 'is-invalid': errors.functionality_id }" :options="sectionsFunctionalitiesList"
                            group-values="functionalities" group-label="name"
                            :placeholder="$t('create_ticket_modal_select_functinoality_placeholder')" label="name"
                            track-by="id">
                        </multiselect>
                        <div v-if="errors.functionality_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.functionality_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="initial_estimation_development_time" class="form-label">{{
                            $t('create_ticket_modal_modal_input_initial_estimation_development_time') }} <strong
                                class="text-danger">*</strong>
                        </label>
                        <input type="number" v-model="formData.initial_estimation_development_time"
                            :class="{ 'is-invalid': errors.initial_estimation_development_time }"
                            id="initial_estimation_development_time" class="form-control">
                        <div v-if="errors.initial_estimation_development_time" class="invalid-feedback">
                            <span v-for="(error, index) in errors.initial_estimation_development_time" :key="index">
                                {{ error }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-desino bg-desino text-light"
                        @click="handleSubmitButtonClick('create_close')">
                        {{ $t('create_ticket_modal_submit_but_create_close_text') }}
                    </button>
                    <button type="submit" class="btn btn-desino bg-desino text-light"
                        @click="handleSubmitButtonClick('create_new')">
                        {{ $t('create_ticket_modal_submit_but_create_add_new_text') }}
                    </button>
                    <button type="submit" class="btn btn-desino bg-desino text-light"
                        @click="handleSubmitButtonClick('create_detail')">
                        {{ $t('create_ticket_modal_submit_but_create_detail_text') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import GlobalMessage from './../../../components/GlobalMessage.vue';
import messageService from '../../../services/messageService';
import TicketService from '../../../services/TicketService';
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import eventBus from '../../../eventBus';
import Multiselect from 'vue-multiselect';

export default {
    name: 'CreateTicketModalComponent',
    components: {
        GlobalMessage,
        Multiselect
    },
    props: {
        selected_initiative_id: Number,
    },
    data() {
        return {
            selectedInitiativeId: null,
            formData: {
                initiative_id: "",
                functionality_id: "",
                type: "",
                initial_estimation_development_time: "",
            },
            submitButtonClicked: '',
            errors: {},
            showMessage: true,
            sectionsFunctionalitiesList: []
        };
    },
    methods: {
        async fetchData() {
            this.selectedInitiativeId = this.$route.params.id;
            this.formData.initiative_id = this.selectedInitiativeId;
            const credentials = {
                initiative_id: this.selectedInitiativeId
            }
            const response = await TicketService.getInitiativeSectionFunctionality(credentials);
            this.sectionsFunctionalitiesList = response.content;
        },
        async storeTicket() {
            this.clearMessages();
            try {
                const response = await TicketService.storeTicket(this.formData);
                // messageService.setMessage(response.data.message, 'success');
                if (this.submitButtonClicked === 'create_close' || this.submitButtonClicked === 'create_detail') {
                    this.hideModal();
                }
                showToast(response.message, 'success');
                this.resetForm();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSubmitButtonClick(buttonValue) {
            this.submitButtonClicked = buttonValue;
        },
        hideModal() {
            const modalElement = document.getElementById('createTicketModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
        },
        resetForm() {
            this.formData = {
                initiative_id: "",
                functionality_id: "",
                type: "",
                initial_estimation_development_time: "",
            };
            this.errors = {};
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
    },
    mounted() {
        this.clearMessages();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
