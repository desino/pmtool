<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="storeTicket">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title font-italic" id="createTicketModalLabel">
                        {{ $t('create_ticket_modal_title') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <input v-model="formData.initiative_id" name="initiative_id" type="hidden">
                    <input v-model="formData.type" name="type" type="hidden">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $t('create_ticket_modal_input_name') }} <strong
                                class="text-danger">*</strong></label>
                        <input v-model="formData.name" :class="{ 'is-invalid': errors.name }" class="form-control"
                            type="text">
                        <div v-if="errors.name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $t('create_ticket_modal_select_type') }} <strong
                                class="text-danger">*</strong></label>
                        <select v-model="formData.type" :class="{ 'is-invalid': errors.type }" class="form-select">
                            <option value="">{{ $t('create_ticket_modal_select_type_placeholder') }}</option>
                            <option v-for="type in ticketTypes" :key="type.id" :value="type.id">{{
                                type.name }}
                            </option>
                        </select>
                        <div v-if="errors.type" class="invalid-feedback">
                            <span v-for="(error, index) in errors.type" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            {{ $t('create_ticket_modal_select_functionality_id') }}
                        </label>
                        <multiselect v-model="formData.functionality_id"
                            :class="{ 'is-invalid': errors.functionality_id }" :options="sectionsFunctionalitiesList"
                            :placeholder="$t('create_ticket_modal_select_functionality_placeholder')"
                            group-label="display_name" group-values="functionalities" label="display_name"
                            track-by="id">
                        </multiselect>
                        <div v-if="errors.functionality_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.functionality_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{
                            $t('create_ticket_modal_modal_input_initial_estimation_development_time')
                        }} <strong class="text-danger">*</strong>
                        </label>
                        <input v-model="formData.initial_estimation_development_time"
                            :class="{ 'is-invalid': errors.initial_estimation_development_time }" class="form-control"
                            type="text">
                        <div v-if="errors.initial_estimation_development_time" class="invalid-feedback">
                            <span v-for="(error, index) in errors.initial_estimation_development_time" :key="index">
                                {{ error }} <br />
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $t('create_ticket_modal_select_project') }}
                        </label>
                        <select v-model="formData.project_id" :class="{ 'is-invalid': errors.project_id }"
                            class="form-select">
                            <option value="">{{ $t('create_ticket_modal_select_project_placeholder') }}</option>
                            <option v-for="project in initiativeProjects" :key="project.id" :value="project.id">{{
                                project.name }}
                            </option>
                        </select>
                        <div v-if="errors.project_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.project_id" :key="index">{{ error }}</span>
                        </div>
                    </div>

                    <div class="row w-100">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" v-model="formData.is_priority" type="checkbox"
                                    id="is_priority">
                                <label class="form-check-label fw-bold" for="is_priority">
                                    {{ $t('create_ticket_modal_checkbox_add_priority_flag') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" v-model="formData.is_visible" type="checkbox"
                                    id="is_visible">
                                <label class="form-check-label fw-bold" for="is_visible">
                                    {{ $t('create_ticket_modal_checkbox_mark_as_visible') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.auto_wait_for_client_approval"
                                :class="{ 'is-invalid': errors.auto_wait_for_client_approval }" type="checkbox"
                                id="auto_wait_for_client_approval">
                            <label class="form-check-label fw-bold" for="auto_wait_for_client_approval">
                                {{ $t('create_ticket_modal_checkbox_auto_wait_for_client_approval') }}
                            </label>
                        </div>
                        <div v-if="errors.auto_wait_for_client_approval" class="invalid-feedback">
                            <span v-for="(error, index) in errors.auto_wait_for_client_approval" :key="index">{{
                                error
                                }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ $t('create_ticket_modal_description') }}
                        </label>
                        <TinyMceEditor v-model="formData.description" :init="{ height: 350, }" />
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $t('create_ticket_modal_card_header_task_actions_text') }}</h6>
                        </div>
                        <div class="card-body">
                            <div v-for="action in formData.ticket_actions" :key="action.id">
                                <div class="row w-100 align-items-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" :class="{ 'is-invalid': errors.actions }"
                                                type="checkbox" :id="'ticket_action_' + action.id" :value="action.id"
                                                v-model="action.is_checked" :disabled="action.is_disabled">
                                            <label class="form-check-label fw-bold" :for="'ticket_action_' + action.id">
                                                {{ action.name }}
                                            </label>
                                        </div>
                                        <div v-if="errors.actions" class="invalid-feedback">
                                            <span v-for="(error, index) in errors.actions" :key="index">{{ error
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <select v-if="action.is_checked"
                                            :class="{ 'is-invalid': errors[`ticket_actions.${action.id}.user_id`] }"
                                            :id="'user_id' + action.id" class="form-select" :value="action.user_id"
                                            @change="updateUser(action.id, $event.target.value)">
                                            <option value="">{{
                                                $t('create_ticket_modal_select_action_user_placeholder')
                                                }}</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors[`ticket_actions.${action.id}.user_id`]"
                                            class="invalid-feedback">
                                            <span
                                                v-for="(error, index) in errors[`ticket_actions.${action.id}.user_id`]"
                                                :key="index">{{
                                                    error
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-4 col-md-4 col-lg-3">
                            <button class="btn btn-desino w-100 border-0" type="submit"
                                @click="handleSubmitButtonClick('create_close')">
                                {{ $t('create_ticket_modal_submit_but_create_close_text') }}
                            </button>
                        </div>
                        <div class="col-4 col-md-4 col-lg-3">
                            <button class="btn btn-desino w-100 border-0" type="submit"
                                @click="handleSubmitButtonClick('create_new')">
                                {{ $t('create_ticket_modal_submit_but_create_add_new_text') }}
                            </button>
                        </div>
                        <div class="col-4 col-md-4 col-lg-3">
                            <button class="btn btn-desino w-100 border-0" type="submit"
                                @click="handleSubmitButtonClick('create_detail')">
                                {{ $t('create_ticket_modal_submit_but_create_detail_text') }}
                            </button>
                        </div>
                        <div class="col-12 col-md-12 col-lg-3">
                            <button class="btn btn-danger w-100 border-0" @click="hideModal" data-bs-dismiss="modal"
                                type="button">
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
import { mapGetters } from "vuex";
import GlobalMessage from './../../../components/GlobalMessage.vue';
import messageService from '../../../services/messageService';
import TicketService from '../../../services/TicketService';
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import Multiselect from 'vue-multiselect';
import { mapActions } from "vuex";
import eventBus from "@/eventBus.js";
import TinyMceEditor from "../../../components/TinyMceEditor.vue";

export default {
    name: 'CreateTicketModalComponent',
    components: {
        GlobalMessage,
        Multiselect,
        TinyMceEditor,
    },
    props: {
        selected_initiative_id: Number,
    },
    computed: {
        ...mapGetters(['user', 'currentInitiative']),
    },
    data() {
        return {
            selectedInitiativeId: null,
            formData: {
                initiative_id: "",
                functionality_id: "",
                project_id: "",
                type: "",
                initial_estimation_development_time: "",
                is_priority: false,
                is_visible: false,
                auto_wait_for_client_approval: false,
                ticket_actions: [],
                description: ''
            },
            ticketTypes: [],
            initiativeProjects: [],
            users: [],
            initiative: [],
            submitButtonClicked: '',
            errors: {},
            showMessage: true,
            sectionsFunctionalitiesList: [],
            ticketActions: [],
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            this.setLoading(true);
            this.selectedInitiativeId = this.$route.params.id ?? this.$route.params.initiative_id;
            this.formData.initiative_id = this.selectedInitiativeId;
            const credentials = {
                initiative_id: this.selectedInitiativeId,
                type: 'create'
            }
            const { content: { sectionFunctionality, ticketTypes, projects, users, actions, initiative } } = await TicketService.getInitialDataForCreateOrEditTicket(credentials);
            this.sectionsFunctionalitiesList = sectionFunctionality;
            this.ticketTypes = ticketTypes;
            this.initiativeProjects = projects;
            this.users = users;
            this.initiative = initiative;
            this.formData.ticket_actions = actions;
            this.ticketActions = JSON.parse(JSON.stringify(actions));
            this.setLoading(false);

        },
        selectedFunctionalityFromFunctionalityList(functionality) {
            this.formData.functionality_id = functionality;
        },
        async storeTicket() {
            this.clearMessages();
            try {
                this.setLoading(true);

                this.formData.initiative_id = this.selectedInitiativeId;
                const response = await TicketService.storeTicket(this.formData);
                if (this.submitButtonClicked === 'create_close') {
                    this.hideModal();
                }
                if (this.submitButtonClicked === 'create_detail') {
                    if (response.content?.asanaTaskData?.permalink_url) {
                        window.open(
                            response.content.asanaTaskData.permalink_url + '/f',
                            '_blank'
                        );
                    }
                }
                await this.setLoading(false);
                if ((this.submitButtonClicked === 'create_new') && (this.user?.is_admin || this.currentInitiative?.functional_owner_id === this.user?.id || this.currentInitiative?.technical_owner_id === this.user?.id)) {
                    const ticketDetailRoute = this.$router.resolve({ name: 'task.detail', params: { initiative_id: response.content.ticket?.initiative_id, ticket_id: response.content.ticket?.id } });
                    window.open(ticketDetailRoute.href, '_blank');
                }

                showToast(response.message, 'success');
                this.resetForm();
                if (this.$route.name === 'tasks') {
                    eventBus.$emit('refreshTickets');
                }
                if (this.$route.name === 'solution-design') {
                    this.$emit('pageUpdated');
                }
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSubmitButtonClick(buttonValue) {
            this.submitButtonClicked = buttonValue;
        },
        updateUser(actionId, userId) {
            const action = this.formData.ticket_actions.find(a => a.action === actionId);
            if (action) {
                action.user_id = userId;
            }
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
            const modalCreateFunctionalityTicketElement = document.getElementById('createFunctionalityTicketModal');
            if (modalCreateFunctionalityTicketElement) {
                const modalCreateFunctionalityTicket = Modal.getInstance(modalCreateFunctionalityTicketElement);
                if (modalCreateFunctionalityTicket) {
                    this.setLoading(false);
                    modalCreateFunctionalityTicket.hide();
                }
            }
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger', 'modal');
            }
            this.setLoading(false);
        },
        resetForm() {
            this.formData = {
                functionality_id: "",
                type: "",
                project_id: "",
                initial_estimation_development_time: "",
                auto_wait_for_client_approval: false,
                ticket_actions: [],
                description: ''
            };
            this.errors = {};
            if (this.submitButtonClicked === 'create_new' || this.submitButtonClicked === 'create_detail') {
                this.formData.ticket_actions = this.ticketActions;
            }
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage('modal');
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
