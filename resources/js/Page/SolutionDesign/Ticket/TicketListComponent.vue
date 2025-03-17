<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <input v-model="filter.task_name" :placeholder="$t('ticket.filter.task_name')"
                            class="form-control" :class="{ 'border-desino border-2': filter.task_name }" type="text"
                            @keyup="fetchAllTasks">
                    </div>
                    <div class="w-100 p-1">
                        <multiselect :class="{ 'multiselect-filter-custom-border': filter.macro_status.length > 0 }"
                            v-model="filter.macro_status" ref="multiselect" :multiple="true"
                            :options="filterMacroStatus" :searchable="true" deselect-label="" label="name"
                            :placeholder="$t('ticket.filter.macro_status_placeholder')" track-by="id"
                            @select="fetchAllTasks" @Remove="fetchAllTasks">
                            <template #tag="{ option, remove }">
                                <span class="multiselect__tag_for_macro_status" :class="option.color">
                                    <span>{{ option.name }}</span>
                                    <i tabindex="1" class="multiselect__tag-icon" @click="remove(option)"></i>
                                </span>
                            </template>
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.task_type" class="form-select" @change="fetchAllTasks"
                            :class="{ 'border-desino border-2': filter.task_type }">
                            <option value="">{{ $t('ticket.filter.task_type_placeholder') }}</option>
                            <option v-for="type in filterTaskTypes" :key="type.id" :value="type.id">{{ type.name }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100 p-1">
                        <multiselect :class="{ 'multiselect-filter-custom-border': filter.functionalities.length > 0 }"
                            v-model="filter.functionalities" ref="multiselect" :multiple="true"
                            :options="functionalities" :searchable="true" deselect-label="" label="display_name"
                            :placeholder="$t('ticket.filter.functionalities_placeholder')" track-by="id"
                            @select="fetchAllTasks" @Remove="fetchAllTasks">
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.action_owner" class="form-select" @change="fetchAllTasks"
                            :class="{ 'border-desino border-2': filter.action_owner }">
                            <option value="">{{ $t('ticket.filter.action_owner_placeholder') }}</option>
                            <option v-for="actionOwner in actionOwners" :key="actionOwner.id" :value="actionOwner.id">{{
                                actionOwner.name }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100 p-1">
                        <select v-model="filter.next_action_owner" class="form-select" @change="fetchAllTasks"
                            :class="{ 'border-desino border-2': filter.next_action_owner }">
                            <option value="">{{ $t('ticket.filter.next_action_owner_placeholder') }}</option>
                            <option v-for="nextActionOwner in nextActionOwners" :key="nextActionOwner.id"
                                :value="nextActionOwner.id">{{
                                    nextActionOwner.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.deployment_id" class="form-select" @change="fetchAllTasks"
                            :class="{ 'border-desino border-2': filter.deployment_id }">
                            <option value="">{{ $t('ticket.filter.deployments_placeholder') }}</option>
                            <option v-for="deployment in filterDeployments" :key="deployment.id" :value="deployment.id">
                                {{
                                    deployment.name }}
                            </option>
                        </select>
                    </div>
                    <div class="w-100 p-1">
                        <multiselect :class="{ 'multiselect-filter-custom-border': filter.projects.length > 0 }"
                            v-model="filter.projects" :multiple="true" :options="projects" :searchable="true"
                            deselect-label="" label="name" :placeholder="$t('ticket.filter.projects_placeholder')"
                            track-by="id" @select="fetchAllTasks" @Remove="fetchAllTasks">
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <div class="form-check ms-auto">
                            <input v-model="filter.is_include_done" @change="fetchAllTasks" class="form-check-input"
                                type="checkbox" id="is_include_done">
                            <label class="form-check-label fw-bold" for="is_include_done">
                                {{ $t('ticket.filter.is_include_done') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="w-100 p-1">
                        <button class="btn btn-desino  w-100" :disabled="selectedTasks.length === 0" type="button"
                            @click="openAssignProjectModal">
                            {{ $t('ticket.assign.project.button_text') }}
                        </button>
                    </div>
                    <div class="w-100 p-1" v-if="createReleaseAllowOrNot()">
                        <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                            @click="openCreateReleaseModal">
                            {{ $t('ticket.release.create.button_text') }}
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                            @click="showConfirmation('addPriorityConfirmation', addRemovePriority, 1)">
                            {{ $t('ticket.add_priority.button_text') }}
                        </button>
                    </div>
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                            @click="showConfirmation('removePriorityConfirmation', addRemovePriority, 0)">
                            {{ $t('ticket.remove_priority.button_text') }}
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                            @click="showConfirmation('markAsVisibleConfirmation', markAsVisibleInvisible, 1)">
                            {{ $t('ticket.mark_as_visible.button_text') }}
                        </button>
                    </div>
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                            @click="showConfirmation('markAsInvisibleConfirmation', markAsVisibleInvisible, 0)">
                            {{ $t('ticket.mark_as_invisible.button_text') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-12 col-md-6 col-lg-9 col-xl-5">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px"></div>
                                <div class="col-auto me-1" style="width:20px">
                                    <input class="form-check-input" type="checkbox" id="chk_all_tickets"
                                        v-model="isChkAllTickets" @change="handleSelectAllTasks">
                                </div>
                                <div class="col-auto" style="width: calc(100% - 40px)">
                                    {{ $t('ticket.list.column_task_name') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xl-2 d-none d-lg-block text-center">
                            {{ $t('ticket.list.column_task_status') }}
                        </div>
                        <div class="col-xl-2 d-none d-xl-block text-end text-xl-center">
                            {{ $t('ticket.list.column_project') }}
                        </div>
                        <div class="col-xl-1 d-none d-xl-block text-start">
                            {{ $t('ticket.list.estimation_hours') }}
                        </div>
                        <div class="col-xl-1 d-none d-xl-block">
                            {{ $t('ticket.list.current_owner') }}
                        </div>
                        <div class="col-xl-1 d-none d-xl-block text-lg-end"></div>
                    </div>
                </li>
                <li v-for="(task, index) in tasks" v-if="tasks.length > 0" :key="task.id"
                    class="list-group-item p-1 list-group-item-action" role="button"
                    :class="backgroundClass(task) + ' ' + (index > 0 ? 'border-top-0' : '')">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 col-md-12 col-lg-9 col-xl-5">
                            <div class="row g-0 h-100 align-items-center" @click="redirectTaskDetailPage(task)">
                                <div class="col-auto me-1" style="width:10px">
                                    <div class="position-absolute" :class="{
                                        'bg-secondary': !task.is_visible,
                                        'bg-warning': task.is_priority && task.is_visible,
                                        '': task.is_visible && !task.is_priority
                                    }" style="width: 10px; height: 100%; left: 0; top: 0;">
                                    </div>
                                </div>
                                <div class="col-auto me-1" style="width:20px">
                                    <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + task.id"
                                        v-model="task.isChecked" @click.stop @change="handleSelectTasks(task)">
                                </div>
                                <div class="col-auto" style="width: calc(100% - 40px)" data-bs-toggle="tooltip"
                                    data-bs-html="true" data-bs-placement="bottom"
                                    :title="tooltipContentForTicketName(task)">
                                    {{ task.composed_name }}
                                </div>
                            </div>
                        </div>
                        <div class="offset-1 col-5 offset-md-1 col-md-3 offset-lg-0 col-lg-3 offset-xl-0 col-xl-2 text-center py-2 py-lg-0" @click="redirectTaskDetailPage(task)">
                            <span class="badge p-2 w-100 text-wrap" :class="task.macro_status_label?.color">
                                {{ task.macro_status_label?.label }}
                            </span>
                        </div>
                        <div class="col-6 col-md-3 col-lg-3 col-xl-2 py-2 py-lg-0">
                            <multiselect @click.stop v-model="task.project" :options="projects" :searchable="true"
                                deselect-label="" label="name" :placeholder="$t('ticket.filter.projects_placeholder')"
                                track-by="id" :ref="'taskProjectDropdowns-' + index"
                                @open="storePreviousProject(task.project, index)"
                                @select="showConfirmation('assign', assignOrRemoveProjectForTask, task.id, index, $event)"
                                @Remove="showConfirmation('remove', assignOrRemoveProjectForTask, task.id, index, $event)">
                            </multiselect>
                        </div>
                        <div class="offset-1 col-3 offset-md-0 col-md-2 col-lg-5 col-xl-1 text-start py-2 py-lg-0"  @click="redirectTaskDetailPage(task)">
                            <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                {{ task.estimation_time }}{{ $t('ticket.list.estimation_hours_text') }}
                            </span>
                        </div>
                        <div class="col-6 col-md-2 col-lg-3 col-xl-1 py-2 py-lg-0"  @click="redirectTaskDetailPage(task)">
                            <span class="badge text-desino d-block d-lg-none px-0 py-2 fw-bold text-start rounded-top">
                                {{ $t('ticket.list.current_owner') }}
                            </span>
                            <span v-if="task?.actions_count != task?.done_actions_count">
                                {{ task?.current_action?.user?.name }}
                            </span>
                            <span v-if="task?.actions_count == task?.done_actions_count">
                                -
                            </span>
                        </div>
                        <div class="col-2 col-md-1 col-lg-1 col-xl-1 text-end py-2 py-lg-0">
                            <div class="dropdown">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="copyToClipboard(task)">
                                            {{ $t('ticket.list.column.action.copy_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1" v-if="task.asana_task_link">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" :href="task.asana_task_link" target="_blank" >
                                            {{ $t('ticket.list.column.action.asana_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-warning w-100 small" href="javascript:" @click="handleTimeBooking(task)" >
                                            {{ $t('ticket_details.time_booking') }}
                                        </a>
                                    </li>
                                    <li class="small" v-if="user?.is_admin" :class="{ 'pb-1': user?.is_admin && task.is_show_delete_btn }">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="editTaskPopup(task)" >
                                            {{ $t('ticket.list.column.action.edit_text') }}
                                        </a>
                                    </li>
                                    <li class="small" v-if="user?.is_admin && task.is_show_delete_btn">
                                        <a role="button" class="btn btn-sm btn-danger w-100 small" href="javascript:" @click="showConfirmation('deleteTicket', deleteTicket, task)">
                                            {{ $t('ticket_details.delete_text') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('ticket.list.not_ticket') }}
                        </div>
                    </div>
                </li>
            </ul>

            <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
                @page-changed="fetchAllTasks" />
        </div>

        <div id="assignProjectModal" aria-hidden="true" aria-labelledby="assignProjectModalLabel" class="modal fade"
            tabindex="-1">
            <AssignProjectModalComponent ref="assignProjectModalComponent" @refreshTickets="fetchAllTasks" />
        </div>
        <div id="editTicketFromListModal" aria-hidden="true" aria-labelledby="editTicketFromListModalLabel"
            class="modal fade" tabindex="-1">
            <EditTicketModalComponent ref="editTicketFromListModalComponent" @refreshTickets="fetchAllTasks" />
        </div>
        <div id="createReleaseModal" aria-hidden="true" aria-labelledby="createReleaseModalLabel" class="modal fade"
            tabindex="-1">
            <CreateReleaseModalComponent ref="createReleaseModalComponent" @refreshTickets="fetchAllTasks" />
        </div>
        <div id="timeBookingForTicketDetailModal" aria-hidden="true" aria-labelledby="timeBookingForTicketDetailLabel"
            class="modal fade" tabindex="-1">
            <TimeBookingForTicketDetailComponent ref="timeBookingForTicketDetailComponent" />
        </div>

        <span id="copyableLink" style="cursor: pointer; text-decoration: underline; color: blue; display: none">
            <a v-bind:href="copyLink">{{ copyLabel }}</a>
        </span>
        <ConfirmationModal ref="dynamicConfirmationModal" :title="modalTitle" :message="modalMessage"
            @confirm="modalConfirmCallback" />
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import { nextTick } from 'vue';
import PaginationComponent from '../../../components/PaginationComponent.vue';
import messageService from '../../../services/messageService';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import ticketService from "../../../services/TicketService";
import Multiselect from "vue-multiselect";
import AssignProjectModalComponent from "./AssignProjectModalComponent.vue";
import { Modal, Tooltip } from 'bootstrap';
import showToast from '../../../utils/toasts';
import eventBus from "@/eventBus.js";
import EditTicketModalComponent from './EditTicketModalComponent.vue';
import CreateReleaseModalComponent from './CreateReleaseModalComponent.vue';
import store from '../../../store';
import TimeBookingForTicketDetailComponent from './TimeBookingForTicketDetailComponent.vue';


export default {
    name: 'TicketListComponent',
    mixins: [globalMixin],
    components: {
        Multiselect,
        GlobalMessage,
        PaginationComponent,
        AssignProjectModalComponent,
        EditTicketModalComponent,
        CreateReleaseModalComponent,
        TimeBookingForTicketDetailComponent
    },
    props: ['id'],
    data() {
        return {
            initiative_id: this.$route.params.id,
            tasks: [],
            currentPage: "",
            totalPages: "",
            filterTaskTypes: [],
            functionalities: [],
            projects: [],
            actionOwners: [],
            nextActionOwners: [],
            filterMacroStatus: [],
            filterDeployments: [],
            filter: {
                task_name: "",
                task_type: "",
                action_owner: "",
                next_action_owner: "",
                functionalities: [],
                projects: [],
                macro_status: [],
                is_open_task: false,
                is_include_done: false,
                deployment_id: "",
            },
            isChkAllTickets: false,
            selectedTasks: [],
            previousProject: null,
            initiative: {},
            prdMacroStatus: "",
            copyLabel: "",
            copyLink: "",
            modalTitle: "",
            modalMessage: "",
            modalConfirmCallback: null,
            errors: {},
            showMessage: true,
            tooltipBtn: null,
        }
    },
    watch: {
        initiative_id: {
            handler(newValue, oldValue) {
                this.resetFilter();
            },
            deep: true
        }
    },
    computed: {
        ...mapGetters(['user', 'passedData']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchAllTasks(page = 1) {
            this.isChkAllTickets = false;
            this.clearMessages();
            this.selectedTasks = [];
            try {
                if (this.passedData.functionality) {
                    this.filter.functionalities.push(this.passedData.functionality);
                    this.filter.is_open_task = true;
                    store.commit("setPassedData", {});
                }
                const params = {
                    page: page,
                    filters: this.filter,
                }
                await this.setLoading(true);
                const response = await ticketService.fetchAllTickets(this.initiative_id, params);
                this.currentPage = response.content.current_page;
                this.totalPages = response.content.last_page;
                this.initiative = response.meta_data.initiative;

                if (response.meta_data.filter_has_value == false) {
                    this.nextActionOwners = response.meta_data.users;
                    this.prdMacroStatus = response.meta_data.prd_macro_status;
                    this.filterDeployments = response.meta_data.deployments;
                    this.filterMacroStatus = response.meta_data.macro_status;
                    this.actionOwners = response.meta_data.users;
                    this.projects = response.meta_data.projects;
                    this.functionalities = response.meta_data.functionalities;
                    this.filterTaskTypes = response.meta_data.task_type;
                }

                this.tasks = response.content.map(task => ({
                    ...task,
                    isChecked: false,
                }));
                await this.setLoading(false);
                setTimeout(() => {
                    const setHeaderData = {
                        page_title: this.$t('ticket.page_title') + ' - ' + this.initiative?.name + ' (#' + response.meta_data.ticket_count + '/' + response.meta_data.ticket_sum + 'hrs)',
                    }
                    store.commit("setHeaderData", setHeaderData);
                }, 100)
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSelectAllTasks() {
            this.selectedTasks = [];
            this.tasks = this.tasks.map(task => {
                task.isChecked = this.isChkAllTickets;
                if (this.isChkAllTickets) {
                    this.selectedTasks.push(task.id);
                }
                return task;
            });
        },
        handleSelectTasks(task) {
            if (task.isChecked) {
                if (!this.selectedTasks.includes(task.id)) {
                    this.selectedTasks.push(task.id);
                }
            } else {
                this.selectedTasks = this.selectedTasks.filter(id => id !== task.id);
            }
            this.isChkAllTickets = this.tasks.every(task => task.isChecked);
        },
        openAssignProjectModal() {
            const passData = {
                tasks: this.selectedTasks,
                initiative_id: this.initiative_id
            }
            this.$refs.assignProjectModalComponent.getSelectedTasksData(passData);
            const modalElement = document.getElementById('assignProjectModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        openCreateReleaseModal() {
            const passData = {
                tasks: this.selectedTasks,
                initiative_id: this.initiative_id
            }
            this.$refs.createReleaseModalComponent.getSelectedTasksData(passData);
            const modalElement = document.getElementById('createReleaseModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        async assignOrRemoveProjectForTask(taskId, type, index, selectedOption) {
            this.clearMessages();
            try {
                const params = {
                    taskId: taskId,
                    type: type,
                    initiative_id: this.initiative_id,
                    selectedOption: selectedOption,
                }
                await this.setLoading(true);
                const { message } = await ticketService.assignOrRemoveProjectForTask(params);
                showToast(message, 'success');
                await this.setLoading(false);
                this.previousProject = selectedOption;
            } catch (error) {
                this.handleError(error);
                this.tasks[index].project = this.previousProject;
            }
        },
        closeDropdown(index) {
            const dropdown = this.$refs['taskProjectDropdowns-' + index][0];
            if (dropdown && dropdown.isOpen) {
                dropdown.deactivate();
            }
        },
        storePreviousProject(previousValue, index) {
            this.previousProject = previousValue;
        },
        editTaskPopup(task) {
            const passData = {
                task_id: task.id,
                initiative_id: this.initiative_id
            }
            this.$refs.editTicketFromListModalComponent.getSelectedTasksData(passData);
            const editTicketFromListModalElement = document.getElementById('editTicketFromListModal');
            if (editTicketFromListModalElement) {
                const editTicketFromListModal = new Modal(editTicketFromListModalElement);
                editTicketFromListModal.show();
            }
        },
        createReleaseAllowOrNot() {
            if (this.user?.id != this.initiative.functional_owner_id) {
                return false;
            }
            return true;
        },
        handleTimeBooking(task) {
            const modalElement = document.getElementById('timeBookingForTicketDetailModal');
            if (modalElement) {
                this.$refs.timeBookingForTicketDetailComponent.getTimeBookingForTicketDetailData(task);
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        async addRemovePriority(isPriority) {
            this.clearMessages();
            try {
                const passData = {
                    initiative_id: this.initiative_id,
                    is_priority: isPriority,
                    ticket_ids: this.selectedTasks
                }
                this.setLoading(true);
                const { message, status } = await ticketService.addRemovePriority(passData);
                showToast(message, 'success');
                this.setLoading(false);
                this.clearMessages();
                this.fetchAllTasks();
            } catch (error) {
                this.handleError(error);
            }
        },
        async markAsVisibleInvisible(isVisible) {

            try {
                const passData = {
                    initiative_id: this.initiative_id,
                    is_visible: isVisible,
                    ticket_ids: this.selectedTasks
                }
                this.setLoading(true);
                const { message, status } = await ticketService.markAsVisibleInvisible(passData);
                showToast(message, 'success');
                this.setLoading(false);
                this.clearMessages();
                this.fetchAllTasks();
            } catch (error) {
                this.handleError(error);
            }
        },
        async deleteTicket(task) {
            if (!this.user?.is_admin) {
                return;
            }

            try {
                const passData = {
                    initiative_id: this.initiative_id,
                    ticket_id: task.id
                }
                this.setLoading(true);
                const { message, status } = await ticketService.deleteTicket(passData);
                showToast(message, 'success');
                this.setLoading(false);
                this.clearMessages();
                this.fetchAllTasks();
            } catch (error) {
                this.handleError(error);
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
        resetFilter() {
            this.filter.task_name = "";
            this.filter.task_type = "";
            this.filter.action_owner = "";
            this.filter.next_action_owner = "";
            this.filter.functionalities = [];
            this.filter.projects = [];
            this.filter.macro_status = [];
            this.filter.is_open_task = false;
            this.filter.is_include_done = false;
            this.filter.deployment_id = "";
        },
        setDeploymentIdForFilter() {
            let deploymentId = "";
            if ('deployment_id' in this.$route.query) {
                deploymentId = this.$route.query.deployment_id;
                this.filter.deployment_id = deploymentId;
                this.filter.is_include_done = true;
            }
        },
        backgroundClass(ticket) {
            if (ticket.release_tickets.length > 0 && this.prdMacroStatus == ticket.macro_status) {
                return 'bg-warning-subtle';
            }
            return '';
        },
        redirectTaskDetailPage(task) {
            const ticketDetailRoute = this.$router.resolve({ name: 'task.detail', params: { initiative_id: this.initiative_id, ticket_id: task.id } });
            window.open(ticketDetailRoute.href, '_blank');
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        tooltipContentForTicketName(task) {
            const createdAtLabel = this.$t('ticket.list.row_hover_tooltip_created_at_text');
            const createdByLabel = this.$t('ticket.list.row_hover_tooltip_created_by_text');
            const commentLabel = this.$t('ticket.list.row_hover_tooltip_comment_text');
            const commentedAtLabel = this.$t('ticket.list.row_hover_tooltip_commented_at_text');
            const releaseNoteLabel = this.$t('ticket.list.row_hover_tooltip_release_note_text');
            let releaseNoteBadgeClass = "bg-danger";
            if (task.is_release_note) {
                releaseNoteBadgeClass = "bg-success";
            }

            let commentHtml = "";
            if (task?.latest_comment?.comment != null) {
                const comment = task?.latest_comment?.comment;
                const userName = task?.latest_comment?.created_updated_user_name;
                const createdAt = task?.latest_comment?.display_updated_at ?? task?.latest_comment?.display_created_at;
                commentHtml = `<strong class='small'>${commentLabel}</strong> <span class="badge bg-secondary d-block-inline text-wrap fst-italic"> ${userName}</span> : <span class="fst-italic">${comment}</span> <strong class='small fst-italic'>${commentedAtLabel}</strong> <span class="badge bg-secondary d-block-inline text-wrap fst-italic">${createdAt}</span>`;
            }
            return `<div class='row g-1 align-items-center small'>
                        <div class='col-12 text-start'>
                            <strong class='small'>${createdByLabel}</strong> <span>${task.display_created_by}</span> <strong class='small'>${createdAtLabel}</strong> <span>${task.display_created_at}</span>
                        </div>
                        <div class='col-12 text-start'>
                            ${commentHtml}
                        </div>
                        <div class='col-12 text-start'>
                            <span class='badge ${releaseNoteBadgeClass} d-block-inline text-wrap fst-italic'>${releaseNoteLabel}</span>
                        </div>
                    </div>`;
        },
        copyToClipboard(task) {
            this.copyLink = `${window.location.origin}/solution-design/${this.initiative_id}/ticket-detail/${task.id}`;
            this.copyLabel = task.composed_name;

            this.$nextTick(() => {
                const linkElement = document.getElementById('copyableLink');
                if (linkElement) {
                    linkElement.style.display = 'inline';

                    const range = document.createRange();
                    range.selectNodeContents(linkElement);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);

                    try {
                        const successful = document.execCommand('copy');
                        if (successful) {
                            showToast(this.$t('ticket.link_copied_to_clipboard'), 'success');
                        } else {
                            showToast(this.$t('ticket.failed_to_copy_link'), 'danger');
                        }
                    } catch (error) {
                        showToast(this.$t('ticket.failed_to_copy_link'), 'danger');
                    }
                    linkElement.style.display = 'none';
                }
            });
        },
        showConfirmation(modalType, callback, callbackParam, index = null, event = null) {
            if (modalType === 'assign' || modalType === 'remove') {
                this.modalTitle = this.$t('ticket.assign_or_remove.project.conformation_popup_title');
                if (modalType === 'assign') {
                    this.modalMessage = this.$t('ticket.assign_or_remove.project.conformation_popup_assign_text');
                } else if (modalType === 'remove') {
                    this.modalMessage = this.$t('ticket.assign_or_remove.project.conformation_popup_remove_text');
                }
            }

            if (modalType === 'addPriorityConfirmation') {
                this.modalTitle = this.$t('ticket.add_priority.button_text');
                this.modalMessage = this.$t('ticket.add_priority.conformation_popup_text');
            } else if (modalType === 'removePriorityConfirmation') {
                this.modalTitle = this.$t('ticket.remove_priority.button_text');
                this.modalMessage = this.$t('ticket.remove_priority.conformation_popup_text');
            }

            if (modalType === 'markAsVisibleConfirmation') {
                this.modalTitle = this.$t('ticket.mark_as_visible.button_text');
                this.modalMessage = this.$t('ticket.is_visible.conformation_popup_text');
            } else if (modalType === 'markAsInvisibleConfirmation') {
                this.modalTitle = this.$t('ticket.mark_as_invisible.button_text');
                this.modalMessage = this.$t('ticket.is_invisible.conformation_popup_text');
            }

            if (modalType === 'deleteTicket') {
                this.modalTitle = this.$t('ticket.delete.conformation_popup_title');
                this.modalMessage = this.$t('ticket.delete.conformation_popup_text');
            }

            this.modalConfirmCallback = () => callback(callbackParam, modalType, index, event);

            this.$refs.dynamicConfirmationModal.showModal();

            if (modalType === 'assign' || modalType === 'remove') {
                const modalElement = document.getElementById('confirmationModal');
                modalElement.addEventListener('hidden.bs.modal', () => {
                    if (!this.$refs.dynamicConfirmationModal.isConfirmed) {
                        this.closeDropdown(index);
                        this.tasks[index].project = this.previousProject;
                    }
                }, { once: true });
            }
        },
    },
    mounted() {
        eventBus.$emit('selectHeaderInitiativeId', this.initiative_id);
        this.setDeploymentIdForFilter();
        this.fetchAllTasks();
        eventBus.$on('refreshTickets', this.fetchAllTasks);
    },
    beforeUnmount() {
        this.showMessage = false;
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.fetchAllTasks();
        this.setDeploymentIdForFilter();
        next();
    },
}
</script>

<!-- <style scoped>
::v-deep(.multiselect__tags) {
    border-color: var(--desino-color, #000) !important;
    border-width: 2px !important;
}
</style> -->
