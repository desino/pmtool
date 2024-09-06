<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="updateTicket">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="createTicketModalLabel" class="modal-title">{{ $t('ticket.edit.modal_title') }}
                    </h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <input v-model="formData.initiative_id" name="initiative_id" type="hidden">
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
                        <label class="form-label" for="type">{{ $t('create_ticket_modal_select_type') }} <strong
                                class="text-danger">*</strong></label>
                        <select v-model="formData.type" :class="{ 'is-invalid': errors.type }" id="type"
                            class="form-select">
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
                        <label class="form-label" for="client_id">
                            {{ $t('create_ticket_modal_select_functionality_id') }}
                        </label>
                        <multiselect v-model="formData.functionality_id"
                            :class="{ 'is-invalid': errors.functionality_id }" :options="sectionsFunctionalitiesList"
                            :placeholder="$t('create_ticket_modal_select_functionality_placeholder')" group-label="name"
                            group-values="functionalities" label="name" track-by="id">
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
                            :class="{ 'is-invalid': errors.initial_estimation_development_time }" class="form-control"
                            type="number">
                        <div v-if="errors.initial_estimation_development_time" class="invalid-feedback">
                            <span v-for="(error, index) in errors.initial_estimation_development_time" :key="index">
                                {{ error }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="project_id">{{ $t('create_ticket_modal_select_project') }}
                        </label>
                        <select v-model="formData.project_id" :class="{ 'is-invalid': errors.project_id }"
                            id="project_id" class="form-select">
                            <option value="">{{ $t('create_ticket_modal_select_project_placeholder') }}</option>
                            <option v-for="project in initiativeProjects" :key="project.id" :value="project.id">{{
                                project.name }}
                            </option>
                        </select>
                        <div v-if="errors.project_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.project_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" v-model="formData.auto_wait_for_client_approval"
                                :class="{ 'is-invalid': errors.auto_wait_for_client_approval }" type="checkbox"
                                id="auto_wait_for_client_approval_edit">
                            <label class="form-check-label" for="auto_wait_for_client_approval_edit">
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
                            <div class="w-100" v-for="action in actions" :key="action.id">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" :class="{ 'is-invalid': errors.actions }"
                                                type="checkbox" :id="'ticket_action_' + action.id" :value="action.id"
                                                v-model="selectedActions" @change="handleActionChange(action.id)"
                                                :disabled="disableActionInput(action.id)">
                                            <label class="form-check-label" :for="'ticket_action_' + action.id">
                                                {{ action.name }}
                                            </label>
                                        </div>
                                        <div v-if="errors.actions" class="invalid-feedback">
                                            <span v-for="(error, index) in errors.actions" :key="index">{{ error
                                                }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3" v-if="isActionSelected(action.id)">
                                        <select :class="{ 'is-invalid': errors[`ticket_actions.${action.id}.user_id`] }"
                                            :id="'user_id' + action.id" class="form-select"
                                            :disabled="disableActionInput(action.id)"
                                            :value="getSelectedUserId(action.id)"
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
                <div class="modal-footer">
                    <button class="btn btn-desino bg-desino text-light" type="submit">
                        {{ $t('ticket.edit.modal_submit_but_text') }}
                    </button>
                    <button class="btn btn-secondary" @click="hideModal" data-bs-dismiss="modal" type="button">{{
                        $t('ticket.edit.modal_close_but_text') }}</button>
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
            actions: [],
            initiative: [],
            selectedActions: [],
            selectedTicketActions: [],
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
                this.actions = actions;
                this.initiative = initiative;
                this.ticket_actions = this.actions;
                // this.selectedTicketActions = selectedTicketActions;
                this.selectedTicketActions = Object.values(selectedTicketActions);
                this.setFormData(ticket);
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
                ticket_actions: []
            };
            this.selectedActions = this.selectedTicketActions.map(action => action.action);
            this.selectedTicketActions.forEach(ticketAction => {
                this.formData.ticket_actions.push({
                    action: ticketAction.action,
                    user_id: ticketAction.user_id,
                    status: ticketAction.status
                });
            });
        },
        handleActionChange(actionId) {
            let selectedUserId = "";
            switch (actionId) {
                case 1:
                    selectedUserId = this.initiative.functional_owner_id ?? "";
                    break;
                case 2:
                case 3:
                    selectedUserId = this.initiative.technical_owner_id ?? "";
                    break;
                case 4:
                    selectedUserId = this.initiative.quality_owner_id ?? "";
                    break;
                case 5:
                    selectedUserId = this.initiative.functional_owner_id ?? "";
                    break;
                default:
                    selectedUserId = "";
            }
            if (this.isActionSelected(actionId)) {
                const selectedTicketAction = this.selectedTicketActions.filter(a => a.action === actionId);
                this.formData.ticket_actions.push({
                    action: actionId,
                    user_id: selectedTicketAction[0]?.user_id ?? selectedUserId,
                    status: selectedTicketAction[0]?.status
                });
            } else {
                this.formData.ticket_actions = this.formData.ticket_actions.filter(a => a.action !== actionId);
            }
        },
        updateUser(actionId, userId) {
            const action = this.formData.ticket_actions.find(a => a.action === actionId);
            if (action) {
                action.user_id = userId;
            }
        },
        getSelectedUserId(actionId) {
            const action = this.formData.ticket_actions.find(a => a.action === actionId);
            return action ? action.user_id : "";
        },
        isActionSelected(actionId) {
            return this.selectedActions.includes(actionId);
        },
        disableActionInput(actionId) {
            const action = this.selectedTicketActions.filter(a => a.action === actionId);
            if (action.length) {
                return action[0].status == 2 ?? false;
            }
            return false;
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
                messageService.setMessage(error.message, 'danger');
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