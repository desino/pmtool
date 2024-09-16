<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('ticket.page_title') }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a class="text-decoration-none" href="javascript:void(0)">
                                {{ $t('home.breadcrumb') }}
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="row mb-3">
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="" class="form-label">{{ $t('ticket.filter.label.task_name') }}</label>
                <input v-model="filter.task_name" :placeholder="$t('ticket.filter.task_name')" class="form-control"
                    type="text" @keyup="fetchAllTasks">
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="" class="form-label">{{ $t('ticket.filter.label.task_type') }}</label>
                <select id="client_id" v-model="filter.task_type" class="form-select" @change="fetchAllTasks">
                    <option value="">{{ $t('ticket.filter.task_type_placeholder') }}</option>
                    <option v-for="type in filterTaskTypes" :key="type.id" :value="type.id">{{ type.name }}
                    </option>
                </select>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="" class="form-label">{{ $t('ticket.filter.label.action_owner') }}</label>
                <select id="client_id" v-model="filter.action_owner" class="form-select" @change="fetchAllTasks">
                    <option value="">{{ $t('ticket.filter.action_owner_placeholder') }}</option>
                    <option v-for="actionOwner in actionOwners" :key="actionOwner.id" :value="actionOwner.id">{{
                        actionOwner.name }}
                    </option>
                </select>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="" class="form-label">{{ $t('ticket.filter.label.next_action_owner') }}</label>
                <select id="client_id" v-model="filter.next_action_owner" class="form-select" @change="fetchAllTasks">
                    <option value="">{{ $t('ticket.filter.next_action_owner_placeholder') }}</option>
                    <option v-for="nextActionOwner in nextActionOwners" :key="nextActionOwner.id"
                        :value="nextActionOwner.id">{{ nextActionOwner.name }}
                    </option>
                </select>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="" class="form-label">{{ $t('ticket.filter.label.functionalities') }}</label>
                <multiselect v-model="filter.functionalities" ref="multiselect" :multiple="true"
                    :options="functionalities" :searchable="true" deselect-label="" label="display_name"
                    :placeholder="$t('ticket.filter.functionalities_placeholder')" track-by="id" @select="fetchAllTasks"
                    @Remove="fetchAllTasks">
                </multiselect>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="" class="form-label">{{ $t('ticket.filter.label.projects') }}</label>
                <multiselect v-model="filter.projects" :multiple="true" :options="projects" :searchable="true"
                    deselect-label="" label="name" :placeholder="$t('ticket.filter.projects_placeholder')" track-by="id"
                    @select="fetchAllTasks" @Remove="fetchAllTasks">
                </multiselect>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <button class="btn btn-desino bg-desino text-light" :disabled="selectedTasks.length === 0" type="button"
                    @click="openAssignProjectModal">
                    {{ $t('ticket.assign.project.button_text') }}
                </button>
                <button v-if="createReleaseAllowOrNot()" class="btn btn-desino bg-desino text-light mx-2"
                    :disabled="selectedTasks.length === 0" type="button" @click="openCreateReleaseModal">
                    {{ $t('ticket.release.create.button_text') }}
                </button>
            </div>
        </div>
        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="font-weight-bold bg-desino text-white rounded-top list-group-item">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        <input class="form-check-input mx-2" type="checkbox" id="chk_all_tickets"
                            v-model="isChkAllTickets" @change="handleSelectAllTasks">
                        {{ $t('ticket.list.column_task_name') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('ticket.list.column_task_status') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                        {{ $t('ticket.list.column_project') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                        {{ $t('ticket.list.current_action') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                        {{ $t('ticket.list.current_owner') }}
                    </div>
                    <div class="col-lg-1 col-md-6 col-6 fw-bold py-2 d-none d-lg-block text-center">
                        {{ $t('ticket.list.column_task_created_at') }}
                    </div>
                    <div class="col-lg-1 col-md-6 col-6 fw-bold py-2 d-none d-lg-block text-lg-end">
                        {{ $t('ticket.list.column_action') }}
                    </div>
                </div>
            </li>
            <li v-for="(task, index) in tasks" v-if="tasks.length > 0" :key="task.id"
                class="border-desino border list-group-item">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-6 col-6 d-flex align-items-center">
                        <div class="mx-2">
                            <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + task.id"
                                v-model="task.isChecked" @change="handleSelectTasks(task)">
                        </div>
                        <div class="mx-2">
                            {{ task.composed_name }}
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">{{ task.status_label }}</div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{ $t('ticket.list.column_project') }} </span>
                        <multiselect v-model="task.project" :options="projects" :searchable="true" deselect-label=""
                            label="name" :placeholder="$t('ticket.filter.projects_placeholder')" track-by="id"
                            :ref="'taskProjectDropdowns-' + index" @open="storePreviousProject(task.project, index)"
                            @select="assignOrRemoveProjectForTask(task.id, 'assign', index, $event)"
                            @Remove="assignOrRemoveProjectForTask(task.id, 'remove', index, $event)">
                        </multiselect>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{ $t('ticket.list.current_action') }} </span>
                        <span v-if="task?.actions_count != task?.done_actions_count">
                            {{ task?.current_action?.action_name }}
                        </span>
                        <span v-if="task?.actions_count == task?.done_actions_count">
                            -
                        </span>
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{ $t('ticket.list.current_owner') }} </span>
                        <span v-if="task?.actions_count != task?.done_actions_count">
                            {{ task?.current_action?.user?.name }}
                        </span>
                        <span v-if="task?.actions_count == task?.done_actions_count">
                            -
                        </span>
                    </div>
                    <div class="col-lg-1 col-md-6 col-6 justify-content-center text-center">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{ $t('ticket.list.column_task_created_at') }} </span>
                        {{ task.display_created_at }}
                    </div>
                    <div class="col-lg-1 col-md-12 col-12 justify-content-end text-end">
                        <router-link
                            :to="{ name: 'task.detail', params: { initiative_id: this.initiative_id, ticket_id: task.id } }"
                            class="text-success me-2">
                            <i class="bi bi-box-arrow-up-right fw-bold"></i>
                        </router-link>
                        <a :title="$t('ticket.list.column.action.edit_text')" class="text-desino me-2"
                            href="javascript:" @click="editTaskPopup(task)">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a v-if="task.asana_task_link" class="text-warning me-2" target="_blank"
                            :href="task.asana_task_link" :title="$t('ticket.list.column.action.asana_task_link_text')">
                            <i class="bi bi-link-45deg fw-bold"></i>
                        </a>
                    </div>
                </div>
            </li>
            <li v-else class="list-group-item border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('ticket.list.not_ticket') }}
                </div>
            </li>
        </ul>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="fetchAllTasks" />

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
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import PaginationComponent from '../../../components/PaginationComponent.vue';
import messageService from '../../../services/messageService';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import ticketService from "../../../services/TicketService";
import Multiselect from "vue-multiselect";
import AssignProjectModalComponent from "./AssignProjectModalComponent.vue";
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import eventBus from "@/eventBus.js";
import EditTicketModalComponent from './EditTicketModalComponent.vue';
import CreateReleaseModalComponent from './CreateReleaseModalComponent.vue';

export default {
    name: 'TicketListComponent',
    mixins: [globalMixin],
    components: {
        Multiselect,
        GlobalMessage,
        PaginationComponent,
        AssignProjectModalComponent,
        EditTicketModalComponent,
        CreateReleaseModalComponent
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
            filter: {
                task_name: "",
                task_type: "",
                action_owner: "",
                next_action_owner: "",
                functionalities: [],
                projects: [],
                is_open_task: false
            },
            isChkAllTickets: false,
            selectedTasks: [],
            previousProject: null,
            initiative: {},
            errors: {},
            showMessage: true
        }
    },
    computed: {
        ...mapGetters(['user', 'passedData']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchAllTasks(page = 1) {
            // console.log('passedData :: ', this.passedData.functionality);
            this.isChkAllTickets = false;
            this.clearMessages();
            this.selectedTasks = [];
            try {
                if (this.passedData.functionality) {
                    this.filter.functionalities.push(this.passedData.functionality);
                    this.filter.is_open_task = true;
                }
                const params = {
                    page: page,
                    filters: this.filter
                }
                await this.setLoading(true);
                const response = await ticketService.fetchAllTickets(this.initiative_id, params);
                this.currentPage = response.content.current_page;
                this.totalPages = response.content.last_page;
                this.filterTaskTypes = response.meta_data.task_type;
                this.functionalities = response.meta_data.functionalities;
                this.projects = response.meta_data.projects;
                this.actionOwners = response.meta_data.users;
                this.nextActionOwners = response.meta_data.users;
                this.initiative = response.meta_data.initiative;
                this.tasks = response.content.data.map(task => ({
                    ...task,
                    isChecked: false,
                }));
                await this.setLoading(false);
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
            const alertText = type == 'assign' ? this.$t('ticket.assign_or_remove.project.conformation_popup_assign_text') : this.$t('ticket.assign_or_remove.project.conformation_popup_remove_text');
            this.$swal({
                title: this.$t('ticket.assign_or_remove.project.conformation_popup_title'),
                text: alertText,
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'bg-desino',
                },
                didClose: () => {
                    this.closeDropdown(index);
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
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
                    } catch (error) {
                        this.handleError(error);
                        this.tasks[index].project = this.previousProject;
                    }
                } else {
                    this.tasks[index].project = this.previousProject;
                }
            }).catch(() => {
                this.tasks[index].project = this.previousProject;
            });
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
        this.fetchAllTasks();
        eventBus.$on('refreshTickets', this.fetchAllTasks);
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
