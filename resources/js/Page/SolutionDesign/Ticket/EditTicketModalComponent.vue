<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="updateTicket">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 id="createTicketModalLabel" class="modal-title">{{ $t('ticket.edit.modal_title') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <input v-model="formData.initiative_id" type="hidden">
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
                                {{ error }} <br>
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
                                    id="is_priority_edit">
                                <label class="form-check-label fw-bold" for="is_priority_edit">
                                    {{ $t('create_ticket_modal_checkbox_add_priority_flag') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" v-model="formData.is_visible" type="checkbox"
                                    id="is_visible_edit">
                                <label class="form-check-label fw-bold" for="is_visible_edit">
                                    {{ $t('create_ticket_modal_checkbox_mark_as_visible') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.auto_wait_for_client_approval"
                                :class="{ 'is-invalid': errors.auto_wait_for_client_approval }" type="checkbox"
                                id="auto_wait_for_client_approval_edit">
                            <label class="form-check-label fw-bold" for="auto_wait_for_client_approval_edit">
                                {{ $t('create_ticket_modal_checkbox_auto_wait_for_client_approval') }}
                            </label>
                        </div>
                        <div v-if="errors.auto_wait_for_client_approval" class="invalid-feedback">
                            <span v-for="(error, index) in errors.auto_wait_for_client_approval" :key="index">{{ error
                                }}</span>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $t('create_ticket_modal_card_header_task_actions_text') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="w-100" v-for="action in formData.ticket_actions" :key="action.id">
                                <div class="row w-100 align-items-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" :class="{ 'is-invalid': errors.actions }"
                                                type="checkbox" :id="'ticket_action_' + action.id" :value="action.id"
                                                v-model="action.is_checked"
                                                :disabled="action.is_disabled || action.is_user_select_box_disabled">
                                            <label class="form-check-label fw-bold" :for="'ticket_action_' + action.id">
                                                {{ action.name }}
                                            </label>
                                        </div>
                                        <div v-if="errors.actions" class="invalid-feedback">
                                            <span v-for="(error, index) in errors.actions" :key="index">{{ error
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" v-if="action.is_checked">
                                        <select :class="{ 'is-invalid': errors[`ticket_actions.${action.id}.user_id`] }"
                                            :id="'user_id' + action.id" class="form-select"
                                            :disabled="action.is_user_select_box_disabled" :value="action.user_id"
                                            @change="updateUser(action.id, $event.target.value)">
                                            <option value="">{{ $t('create_ticket_modal_select_action_user_placeholder')
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
                        <div class="col-4 col-md-4 col-lg-6">
                            <button class="btn btn-desino w-100 border-0" type="submit">
                                {{ $t('ticket.edit.modal_submit_but_text') }}
                            </button>
                        </div>
                        <div class="col-4 col-md-4 col-lg-6">
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
import globalMixin from '@/globalMixin';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions } from "vuex";
import { Modal } from 'bootstrap';
import messageService from '../../../services/messageService';
import TicketService from '../../../services/TicketService';
import Multiselect from 'vue-multiselect';
import showToast from '../../../utils/toasts';
import eventBus from '../../../eventBus';
export default {
    name: 'EditTicketModalComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        Multiselect
    },
    data() {
        return {
            formData: {
                id: "",
                name: "",
                initiative_id: "",
                functionality_id: "",
                project_id: "",
                type: "",
                initial_estimation_development_time: "",
                auto_wait_for_client_approval: false,
                ticket_actions: []
            },
            ticketTypes: [],
            sectionsFunctionalitiesList: [],
            initiativeProjects: [],
            users: [],
            initiative: [],
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        getSelectedTasksData(data) {
            this.getEditData(data);
        },

        async getEditData(data) {
            this.clearMessages();
            try {
                const passData = {
                    id: data.task_id,
                    initiative_id: data.initiative_id
                }
                this.setLoading(true);
                const { content: { sectionFunctionality, ticketTypes, projects, users, actions, initiative, ticket, selectedTicketActions } } = await TicketService.getEditTicket(passData);
                this.sectionsFunctionalitiesList = sectionFunctionality;
                this.ticketTypes = ticketTypes;
                this.initiativeProjects = projects;
                this.users = users;
                this.initiative = initiative;
                this.setFormData(ticket);
                this.formData.ticket_actions = actions;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        setFormData(ticket) {
            this.formData = {
                id: ticket.id,
                initiative_id: ticket.initiative_id,
                name: ticket.name,
                type: ticket.type,
                functionality_id: ticket.functionality,
                initial_estimation_development_time: ticket.initial_estimation_development_time,
                project_id: ticket.project_id,
                auto_wait_for_client_approval: ticket.auto_wait_for_client_approval == 1 ?? false,
                is_priority: ticket.is_priority == 1 ?? false,
                is_visible: ticket.is_visible == 1 ?? false,
            };
        },
        updateUser(actionId, userId) {
            const action = this.formData.ticket_actions.find(a => a.action === actionId);
            if (action) {
                action.user_id = userId;
            }
        },
        resetForm() {
            this.formData = {
                functionality_id: "",
                type: "",
                project_id: "",
                initial_estimation_development_time: "",
                auto_wait_for_client_approval: false,
                ticket_actions: []
            };
            this.selectedActions = [];
            this.errors = {};
        },
        async updateTicket() {
            this.clearMessages();
            try {
                this.setLoading(true);
                // this.formData.initiative_id = this.selectedInitiativeId;
                const response = await TicketService.updateTicket(this.formData);
                this.hideModal();
                this.setLoading(false);
                showToast(response.message, 'success');
                this.resetForm();
                if (this.$route.name === 'tasks') {
                    eventBus.$emit('refreshTickets');
                }
                // if (this.$route.name === 'task.detail') {
                //     eventBus.$emit('refreshTicketDetail');
                // }
            } catch (error) {
                this.handleError(error);
            }
        },
        hideModal() {
            const modalElement = document.getElementById('editTicketFromListModal');
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
                messageService.setMessage(error.message, 'danger', 'modal');
            }
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
    },
    mounted() {
        this.clearMessages();
        this.setLoading(false);
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>