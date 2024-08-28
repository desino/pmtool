<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="storeTicket">
            <div class="modal-content">
                <div v-if="showCreateTicketModalContent">
                    <div class="modal-header">
                        <h5 id="createTicketModalLabel" class="modal-title">{{ $t('create_ticket_modal_title') }}
                        </h5>
                        <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                    </div>
                    <div class="modal-body">
                        <GlobalMessage v-if="showMessage" />
                        <input v-model="formData.initiative_id" name="initiative_id" type="hidden">
                        <input v-model="formData.type" name="type" type="hidden">
                        <div class="mb-3">
                            <label class="form-label" for="name">{{ $t('create_ticket_modal_input_name') }} <strong
                                    class="text-danger">*</strong></label>
                            <input id="name" v-model="formData.name" :class="{ 'is-invalid': errors.name }"
                                class="form-control" type="text">
                            <div v-if="errors.name" class="invalid-feedback">
                                <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="client_id">{{
                                $t('create_ticket_modal_select_functionality_id')
                                }}
                                <strong class="text-danger">*</strong></label>
                            <multiselect v-model="formData.functionality_id"
                                :class="{ 'is-invalid': errors.functionality_id }"
                                :options="sectionsFunctionalitiesList"
                                :placeholder="$t('create_ticket_modal_select_functionality_placeholder')"
                                group-label="name" group-values="functionalities" label="name" track-by="id">
                            </multiselect>
                            <div v-if="errors.functionality_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.functionality_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="initial_estimation_development_time">{{
                                $t('create_ticket_modal_modal_input_initial_estimation_development_time')
                                }} <strong class="text-danger">*</strong>
                            </label>
                            <input id="initial_estimation_development_time"
                                v-model="formData.initial_estimation_development_time"
                                :class="{ 'is-invalid': errors.initial_estimation_development_time }"
                                class="form-control" type="number">
                            <div v-if="errors.initial_estimation_development_time" class="invalid-feedback">
                                <span v-for="(error, index) in errors.initial_estimation_development_time" :key="index">
                                    {{ error }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-desino bg-desino text-light" type="submit"
                            @click="handleSubmitButtonClick('create_close')">
                            {{ $t('create_ticket_modal_submit_but_create_close_text') }}
                        </button>
                        <button class="btn btn-desino bg-desino text-light" type="submit"
                            @click="handleSubmitButtonClick('create_new')">
                            {{ $t('create_ticket_modal_submit_but_create_add_new_text') }}
                        </button>
                        <button class="btn btn-desino bg-desino text-light" type="submit"
                            @click="handleSubmitButtonClick('create_detail')">
                            {{ $t('create_ticket_modal_submit_but_create_detail_text') }}
                        </button>
                        <button class="btn btn-secondary" @click="hideModal" data-bs-dismiss="modal"
                            type="button">Close</button>
                    </div>
                </div>
                <div v-if="showAsanaIframContent">
                    <div class="modal-body">
                        <iframe :src="iframeAsanaTaskDetailSrc" width="600" height="400" frameborder="0"
                            allowfullscreen></iframe>
                    </div>
                    <div class="modal-footer">
                        <!-- ddgdfgdf -->
                    </div>
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
import Multiselect from 'vue-multiselect';
import { mapActions } from "vuex";

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
            showCreateTicketModalContent: true,
            showAsanaIframContent: false,
            iframeAsanaTaskDetailSrc: '',
            errors: {},
            showMessage: true,
            sectionsFunctionalitiesList: []
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            this.setLoading(true);
            this.selectedInitiativeId = this.$route.params.id ?? this.$route.params.initiative_id;
            this.formData.initiative_id = this.selectedInitiativeId;
            const credentials = {
                initiative_id: this.selectedInitiativeId
            }
            const response = await TicketService.getInitiativeSectionFunctionality(credentials);
            this.sectionsFunctionalitiesList = response.content;
            this.setLoading(false);
        },
        async storeTicket() {
            this.clearMessages();
            try {
                this.setLoading(true);

                this.formData.initiative_id = this.selectedInitiativeId;
                // const response = await TicketService.storeTicket(this.formData);
                // messageService.setMessage(response.data.message, 'success');
                if (this.submitButtonClicked === 'create_close') {
                    this.hideModal();
                }
                if (this.submitButtonClicked === 'create_detail') {
                    this.showCreateTicketModalContent = false;
                    this.showAsanaIframContent = true;
                    // this.iframeAsanaTaskDetailSrc = "https://app.asana.com/0/1207884997541645/1208126964022359/f";
                    this.iframeAsanaTaskDetailSrc = "https://www.google.com/";
                    // this.hideModal();
                    // this.$router.push({
                    //     name: 'task.detail',
                    //     params: { initiative_id: this.formData.initiative_id, ticket_id: response.content.ticket.id }
                    // });
                }
                this.setLoading(false);

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
                    this.setLoading(false);
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
            this.setLoading(false);
        },
        resetForm() {
            this.formData = {
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
