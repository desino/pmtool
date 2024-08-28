<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('ticket.page_title') }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a class="text-decoration-none" href="javascript:void(0)">{{
                            $t('Dashboard') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="row mb-3">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <input v-model="filter.task_name" :placeholder="$t('ticket.filter.task_name')" class="form-control"
                    type="text" @keyup="fetchAllTasks">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select id="client_id" v-model="filter.task_type" class="form-select" @change="fetchAllTasks">
                    <option value="">{{ $t('ticket.filter.task_type_placeholder') }}</option>
                    <option v-for="(name, id) in filterTaskTypes" :key="id" :value="id">{{ name }}
                    </option>
                </select>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <multiselect v-model="filter.functionalities" :multiple="true" :options="functionalities"
                    :searchable="true" deselect-label="" label="display_name"
                    :placeholder="$t('ticket.filter.functionalities_placeholder')" track-by="id" @select="fetchAllTasks"
                    @Remove="fetchAllTasks">
                </multiselect>
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
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
            </div>
        </div>
        <div class="list-group-item mx-2 mb-3 mt-2">
            <div class="row justify-content-between font-weight-bold bg-desino text-white rounded-top">
                <div class="col-lg-1 col-md-6 col-6 fw-bold py-2">
                    <input class="form-check-input" type="checkbox" id="chk_all_tickets" v-model="isChkAllTickets"
                        @change="handleSelectAllTasks">
                </div>
                <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                    {{ $t('ticket.list.column_task_name') }}
                </div>
                <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                    {{ $t('ticket.list.column_task_type') }}
                </div>
                <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                    {{ $t('ticket.list.column_project') }}
                </div>
                <div class="col-lg-3 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                    {{ $t('ticket.list.column_task_created_at') }}
                </div>
                <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-flex justify-content-end align-items-end">
                    {{ $t('ticket.list.column_action') }}
                </div>
            </div>
            <div v-for="task in tasks" v-if="tasks.length > 0" :key="task.id">
                <div class="row justify-content-between border-desino border">
                    <div class="col-lg-1 col-md-6 col-6 py-1">
                        <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + task.id"
                            v-model="task.isChecked" @change="handleSelectTasks(task)">
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">{{ task.name }}</div>
                    <div class="col-lg-2 col-md-6 col-6">{{ task.type_label }}</div>
                    <div class="col-lg-2 col-md-6 col-6 py-1">
                        <!-- {{ task.project?.name }} -->
                        <multiselect v-model="task.project" :options="projects" :searchable="true" deselect-label=""
                            label="name" :placeholder="$t('ticket.filter.projects_placeholder')" track-by="id"
                            @select="updateProjectTask">
                        </multiselect>
                    </div>
                    <div class="col-lg-3 col-md-6 col-8 text-center text-lg-start">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{ $t('ticket.list.column_task_created_at') }} </span>
                        {{ task.display_created_at }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-4 d-flex justify-content-end align-items-end">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 text-white text-center">
                            {{ $t('ticket.list.column_action') }}</span>
                        <router-link
                            :to="{ name: 'task.detail', params: { initiative_id: this.initiative_id, ticket_id: task.id } }"
                            class="text-success me-2">
                            <i class="bi bi-box-arrow-up-right fw-bold"></i>
                        </router-link>

                    </div>
                </div>
            </div>
            <div v-else class="list-group-item row border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('ticket.list.not_ticket') }}
                </div>
            </div>
        </div>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="fetchAllTasks" />

        <div id="assignProjectModal" aria-hidden="true" aria-labelledby="assignProjectModalLabel" class="modal fade"
            tabindex="-1">
            <AssignProjectModalComponent ref="assignProjectModalComponent" @refreshTickets="fetchAllTasks" />
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import PaginationComponent from '../../../components/PaginationComponent.vue';
import messageService from '../../../services/messageService';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions } from 'vuex';
import ticketService from "../../../services/TicketService";
import Multiselect from "vue-multiselect";
import AssignProjectModalComponent from "./AssignProjectModalComponent.vue";
import { Modal } from 'bootstrap';

export default {
    name: 'TicketListComponent',
    mixins: [globalMixin],
    components: {
        Multiselect,
        GlobalMessage,
        PaginationComponent,
        AssignProjectModalComponent
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
            filter: {
                task_name: "",
                task_type: "",
                functionalities: [],
                projects: [],
            },
            isChkAllTickets: false,
            selectedTasks: [],
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchAllTasks(page = 1) {
            this.clearMessages();
            this.selectedTasks = [];
            try {
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
        updateProjectTask(selectedOption, id) {
            console.log('id :: ', id);
            console.log('project :: ', selectedOption);
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
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
