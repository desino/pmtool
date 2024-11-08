<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content my-3">
        <div class="row g-0 w-100 py-2">
            <div class="col-12 col-md-12 col-lg-3">
                <div class="w-100 p-1">
                    <input v-model="filter.task_name" :placeholder="$t('ticket.filter.task_name')" class="form-control"
                        type="text" @keyup="fetchAllTasks">
                </div>
                <div class="w-100 p-1">
                    <multiselect v-model="filter.macro_status" ref="multiselect" :multiple="true"
                        :options="filterMacroStatus" :searchable="true" deselect-label="" label="name"
                        :placeholder="$t('ticket.filter.macro_status_placeholder')" track-by="id"
                        @select="fetchAllTasks" @Remove="fetchAllTasks">
                        <template #tag="{ option, remove }">
                            <span class="multiselect__tag_for_macro_status" :class="'bg-' + option.color">
                                <span>{{ option.name }}</span>
                                <i tabindex="1" class="multiselect__tag-icon" @click="remove(option)"></i>
                            </span>
                        </template>
                    </multiselect>
                </div>
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
            <div class="col-12 col-md-6 col-lg-3">
                <div class="w-100 p-1">
                    <select v-model="filter.task_type" class="form-select" @change="fetchAllTasks">
                        <option value="">{{ $t('ticket.filter.task_type_placeholder') }}</option>
                        <option v-for="type in filterTaskTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                </div>
                <div class="w-100 p-1">
                    <multiselect v-model="filter.functionalities" ref="multiselect" :multiple="true"
                        :options="functionalities" :searchable="true" deselect-label="" label="display_name"
                        :placeholder="$t('ticket.filter.functionalities_placeholder')" track-by="id"
                        @select="fetchAllTasks" @Remove="fetchAllTasks">
                    </multiselect>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="w-100 p-1">
                    <select v-model="filter.action_owner" class="form-select" @change="fetchAllTasks">
                        <option value="">{{ $t('ticket.filter.action_owner_placeholder') }}</option>
                        <option v-for="actionOwner in actionOwners" :key="actionOwner.id" :value="actionOwner.id">{{
                            actionOwner.name }}
                        </option>
                    </select>
                </div>
                <div class="w-100 p-1">
                    <select v-model="filter.next_action_owner" class="form-select" @change="fetchAllTasks">
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
                    <select v-model="filter.deployment_id" class="form-select" @change="fetchAllTasks">
                        <option value="">{{ $t('ticket.filter.deployments_placeholder') }}</option>
                        <option v-for="deployment in filterDeployments" :key="deployment.id" :value="deployment.id">{{
                            deployment.name }}
                        </option>
                    </select>
                </div>
                <div class="w-100 p-1">
                    <multiselect v-model="filter.projects" :multiple="true" :options="projects" :searchable="true"
                        deselect-label="" label="name" :placeholder="$t('ticket.filter.projects_placeholder')"
                        track-by="id" @select="fetchAllTasks" @Remove="fetchAllTasks">
                    </multiselect>
                </div>
            </div>
        </div>
        <div class="row g-0 w-100 py-2">
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
                        @click="addRemovePriority(1)">
                        {{ $t('ticket.add_priority.button_text') }}
                    </button>
                </div>
                <div class="w-100 p-1">
                    <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                        @click="addRemovePriority(0)">
                        {{ $t('ticket.remove_priority.button_text') }}
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="w-100 p-1">
                    <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                        @click="markAsVisibleInvisible(1)">
                        {{ $t('ticket.mark_as_visible.button_text') }}
                    </button>
                </div>
                <div class="w-100 p-1">
                    <button class="btn btn-desino w-100" :disabled="selectedTasks.length === 0" type="button"
                        @click="markAsVisibleInvisible(0)">
                        {{ $t('ticket.mark_as_invisible.button_text') }}
                    </button>
                </div>
            </div>
        </div>
        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="list-group-item bg-desino text-white border-0 rounded-top px-0 py-3">
                <div class="row w-100 align-items-center">
                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="row g-0 h-100 align-items-center">
                            <div class="col-auto me-1" style="width:10px"></div>
                            <div class="col-auto me-1" style="width:20px">
                                <input class="form-check-input" type="checkbox" id="chk_all_tickets"
                                    v-model="isChkAllTickets" @change="handleSelectAllTasks">
                            </div>
                            <div class="col-auto fw-bold small" style="width: calc(100% - 40px)">
                                {{ $t('ticket.list.column_task_name') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 fw-bold small">
                        {{ $t('ticket.list.column_task_status') }}
                    </div>
                    <div class="col-lg-2 col-md-3 fw-bold small d-none d-md-block">
                        {{ $t('ticket.list.column_project') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold small d-none d-lg-block">
                        {{ $t('ticket.list.current_action') }}
                    </div>
                    <div class="col-lg-1 col-md-6 col-6 fw-bold small d-none d-lg-block">
                        {{ $t('ticket.list.current_owner') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold small d-none d-lg-block text-lg-end">
                        {{ $t('ticket.list.column_action') }}
                    </div>
                </div>
            </li>
            <li v-for="(task, index) in tasks" v-if="tasks.length > 0" :key="task.id"
                class="border list-group-item px-0 py-1 list-group-item-action" role="button"
                :class="backgroundClass(task) + ' ' + (index > 0 ? 'border-top-0' : '')">
                <div class="row w-100 align-items-center">
                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="row g-0 h-100 align-items-center">
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
                                    v-model="task.isChecked" @change="handleSelectTasks(task)">
                            </div>
                            <div class="col-auto" style="width: calc(100% - 40px)">
                                {{ task.composed_name }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-6 text-center">
                        <span class="badge p-2 w-100 text-wrap" :class="'bg-' + task.macro_status_label?.color">{{
                            task.macro_status_label?.label }}</span>
                    </div>
                    <div class="offset-1 offset-md-0 col-lg-2 col-md-3 col-12">
                        <span class="badge text-desino d-block d-md-none p-2 fw-bold text-center rounded-top">
                            {{ $t('ticket.list.column_project') }}
                        </span>
                        <multiselect v-model="task.project" :options="projects" :searchable="true" deselect-label=""
                            label="name" :placeholder="$t('ticket.filter.projects_placeholder')" track-by="id"
                            :ref="'taskProjectDropdowns-' + index" @open="storePreviousProject(task.project, index)"
                            @select="assignOrRemoveProjectForTask(task.id, 'assign', index, $event)"
                            @Remove="assignOrRemoveProjectForTask(task.id, 'remove', index, $event)">
                        </multiselect>
                    </div>
                    <div class="offset-1 offset-md-1 offset-lg-0 col-lg-2 col-md-4 col-4 text-start text-md-center">
                        <span class="badge text-desino d-block d-lg-none p-2 fw-bold text-center rounded-top">
                            {{ $t('ticket.list.current_action') }}
                        </span>
                        <span v-if="task?.actions_count != task?.done_actions_count">
                            {{ task?.current_action?.action_name }}
                        </span>
                        <span v-if="task?.actions_count == task?.done_actions_count">
                            -
                        </span>
                    </div>
                    <div class="col-lg-2 col-md-4 col-4">
                        <span class="badge text-desino d-block d-lg-none p-2 fw-bold text-center rounded-top">
                            {{ $t('ticket.list.current_owner') }}
                        </span>
                        <span v-if="task?.actions_count != task?.done_actions_count">
                            {{ task?.current_action?.user?.name }}
                        </span>
                        <span v-if="task?.actions_count == task?.done_actions_count">
                            -
                        </span>
                    </div>
                    <div v-if="user?.is_admin" class="col-lg-1 col-md-3 col-3 justify-content-end text-end">
                        <span class="badge text-desino d-block d-lg-none p-2 fw-bold text-center rounded-top">
                            {{ $t('ticket.list.column_action') }}
                        </span>
                        <router-link
                            :to="{ name: 'task.detail', params: { initiative_id: this.initiative_id, ticket_id: task.id } }"
                            class="text-success me-1">
                            <i class="bi bi-box-arrow-up-right fw-bold"></i>
                        </router-link>
                        <a :title="$t('ticket.list.column.action.edit_text')" class="text-desino me-1"
                            href="javascript:" @click="editTaskPopup(task)">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a class="me-1" v-if="task.asana_task_link" :href="task.asana_task_link" target="_blank">
                            <svg fill="none" height="21px" viewBox="0 0 24 24" width="21px"
                                xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                    d="M10.4693 3.55448C10.9546 3.35346 11.4747 3.25 12 3.25C12.5253 3.25 13.0454 3.35346 13.5307 3.55448C14.016 3.7555 14.457 4.05014 14.8284 4.42157C15.1999 4.79301 15.4945 5.23397 15.6955 5.71927C15.8965 6.20457 16 6.72471 16 7.25C16 7.77529 15.8965 8.29543 15.6955 8.78073C15.4945 9.26603 15.1999 9.70699 14.8284 10.0784C14.457 10.4499 14.016 10.7445 13.5307 10.9455C13.0454 11.1465 12.5253 11.25 12 11.25C11.4747 11.25 10.9546 11.1465 10.4693 10.9455C9.98396 10.7445 9.54301 10.4499 9.17157 10.0784C8.80014 9.70699 8.5055 9.26604 8.30448 8.78073C8.10346 8.29543 8 7.77529 8 7.25C8 6.72471 8.10346 6.20457 8.30448 5.71927C8.5055 5.23396 8.80014 4.79301 9.17157 4.42157C9.54301 4.05014 9.98396 3.7555 10.4693 3.55448ZM12 4.75C11.6717 4.75 11.3466 4.81466 11.0433 4.9403C10.74 5.06594 10.4644 5.25009 10.2322 5.48223C10.0001 5.71438 9.81594 5.98998 9.6903 6.29329C9.56466 6.59661 9.5 6.92169 9.5 7.25C9.5 7.5783 9.56466 7.90339 9.6903 8.20671C9.81594 8.51002 10.0001 8.78562 10.2322 9.01777C10.4644 9.24991 10.74 9.43406 11.0433 9.5597C11.3466 9.68534 11.6717 9.75 12 9.75C12.3283 9.75 12.6534 9.68534 12.9567 9.5597C13.26 9.43406 13.5356 9.24991 13.7678 9.01777C13.9999 8.78562 14.1841 8.51002 14.3097 8.20671C14.4353 7.90339 14.5 7.5783 14.5 7.25C14.5 6.9217 14.4353 6.59661 14.3097 6.29329C14.1841 5.98998 13.9999 5.71438 13.7678 5.48223C13.5356 5.25009 13.26 5.06594 12.9567 4.9403C12.6534 4.81466 12.3283 4.75 12 4.75Z"
                                    fill="#ffc107" fill-rule="evenodd" />
                                <path clip-rule="evenodd"
                                    d="M5.46927 12.5545C5.95457 12.3535 6.47471 12.25 7 12.25C7.52529 12.25 8.04543 12.3535 8.53073 12.5545C9.01604 12.7555 9.45699 13.0501 9.82843 13.4216C10.1999 13.793 10.4945 14.234 10.6955 14.7193C10.8965 15.2046 11 15.7247 11 16.25C11 16.7753 10.8965 17.2954 10.6955 17.7807C10.4945 18.266 10.1999 18.707 9.82843 19.0784C9.45699 19.4499 9.01604 19.7445 8.53073 19.9455C8.04543 20.1465 7.52529 20.25 7 20.25C6.47471 20.25 5.95457 20.1465 5.46927 19.9455C4.98396 19.7445 4.54301 19.4499 4.17157 19.0784C3.80014 18.707 3.5055 18.266 3.30448 17.7807C3.10346 17.2954 3 16.7753 3 16.25C3 15.7247 3.10346 15.2046 3.30448 14.7193C3.5055 14.234 3.80014 13.793 4.17157 13.4216C4.54301 13.0501 4.98396 12.7555 5.46927 12.5545ZM7 13.75C6.67169 13.75 6.34661 13.8147 6.04329 13.9403C5.73998 14.0659 5.46438 14.2501 5.23223 14.4822C5.00009 14.7144 4.81594 14.99 4.6903 15.2933C4.56466 15.5966 4.5 15.9217 4.5 16.25C4.5 16.5783 4.56466 16.9034 4.6903 17.2067C4.81594 17.51 5.00009 17.7856 5.23223 18.0178C5.46438 18.2499 5.73998 18.4341 6.04329 18.5597C6.34661 18.6853 6.67169 18.75 7 18.75C7.3283 18.75 7.65339 18.6853 7.95671 18.5597C8.26002 18.4341 8.53562 18.2499 8.76777 18.0178C8.99991 17.7856 9.18406 17.51 9.3097 17.2067C9.43534 16.9034 9.5 16.5783 9.5 16.25C9.5 15.9217 9.43534 15.5966 9.3097 15.2933C9.18406 14.99 8.99991 14.7144 8.76777 14.4822C8.53562 14.2501 8.26002 14.0659 7.95671 13.9403C7.65339 13.8147 7.3283 13.75 7 13.75Z"
                                    fill="#ffc107" fill-rule="evenodd" />
                                <path clip-rule="evenodd"
                                    d="M17 12.25C16.4747 12.25 15.9546 12.3535 15.4693 12.5545C14.984 12.7555 14.543 13.0501 14.1716 13.4216C13.8001 13.793 13.5055 14.234 13.3045 14.7193C13.1035 15.2046 13 15.7247 13 16.25C13 16.7753 13.1035 17.2954 13.3045 17.7807C13.5055 18.266 13.8001 18.707 14.1716 19.0784C14.543 19.4499 14.984 19.7445 15.4693 19.9455C15.9546 20.1465 16.4747 20.25 17 20.25C17.5253 20.25 18.0454 20.1465 18.5307 19.9455C19.016 19.7445 19.457 19.4499 19.8284 19.0784C20.1999 18.707 20.4945 18.266 20.6955 17.7807C20.8965 17.2954 21 16.7753 21 16.25C21 15.7247 20.8965 15.2046 20.6955 14.7193C20.4945 14.234 20.1999 13.793 19.8284 13.4216C19.457 13.0501 19.016 12.7555 18.5307 12.5545C18.0454 12.3535 17.5253 12.25 17 12.25ZM16.0433 13.9403C16.3466 13.8147 16.6717 13.75 17 13.75C17.3283 13.75 17.6534 13.8147 17.9567 13.9403C18.26 14.0659 18.5356 14.2501 18.7678 14.4822C18.9999 14.7144 19.1841 14.99 19.3097 15.2933C19.4353 15.5966 19.5 15.9217 19.5 16.25C19.5 16.5783 19.4353 16.9034 19.3097 17.2067C19.1841 17.51 18.9999 17.7856 18.7678 18.0178C18.5356 18.2499 18.26 18.4341 17.9567 18.5597C17.6534 18.6853 17.3283 18.75 17 18.75C16.6717 18.75 16.3466 18.6853 16.0433 18.5597C15.74 18.4341 15.4644 18.2499 15.2322 18.0178C15.0001 17.7856 14.8159 17.51 14.6903 17.2067C14.5647 16.9034 14.5 16.5783 14.5 16.25C14.5 15.9217 14.5647 15.5966 14.6903 15.2933C14.8159 14.99 15.0001 14.7144 15.2322 14.4822C15.4644 14.2501 15.74 14.0659 16.0433 13.9403Z"
                                    fill="#ffc107" fill-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="javascript:" @click="handleTimeBooking(task)"
                            :title="$t('ticket_details.time_booking')">
                            <i class="bi bi-clock-history"></i>
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
        <div id="timeBookingForTicketDetailModal" aria-hidden="true" aria-labelledby="timeBookingForTicketDetailLabel"
            class="modal fade" tabindex="-1">
            <TimeBookingForTicketDetailComponent ref="timeBookingForTicketDetailComponent" />
        </div>
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
import { Modal } from 'bootstrap';
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
                this.filterTaskTypes = response.meta_data.task_type;
                this.filterDeployments = response.meta_data.deployments;
                this.functionalities = response.meta_data.functionalities;
                this.projects = response.meta_data.projects;
                this.actionOwners = response.meta_data.users;
                this.nextActionOwners = response.meta_data.users;
                this.initiative = response.meta_data.initiative;
                this.filterMacroStatus = response.meta_data.macro_status;
                this.prdMacroStatus = response.meta_data.prd_macro_status;
                this.tasks = response.content.data.map(task => ({
                    ...task,
                    isChecked: false,
                }));
                const setHeaderData = {
                    page_title: this.$t('ticket.page_title') + ' - ' + this.initiative?.name,
                }
                store.commit("setHeaderData", setHeaderData);
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
                    confirmButton: 'btn-desino',
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
        handleTimeBooking(task) {
            const modalElement = document.getElementById('timeBookingForTicketDetailModal');
            if (modalElement) {
                this.$refs.timeBookingForTicketDetailComponent.getTimeBookingForTicketDetailData(task);
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        async addRemovePriority(isPriority) {
            let alertText = '';
            if (isPriority) {
                alertText = this.$t('ticket.add_priority.conformation_popup_text');
            } else {
                alertText = this.$t('ticket.remove_priority.conformation_popup_text');
            }
            this.$swal({
                title: this.$t('ticket.priority.conformation_popup_title'),
                text: alertText,
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'btn-desino',
                },
            }).then(async (result) => {
                if (result.isConfirmed) {
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
                        this.tasks[index].project = this.previousProject;
                    }
                }
            })
        },
        async markAsVisibleInvisible(isVisible) {
            let alertText = '';
            if (isVisible) {
                alertText = this.$t('ticket.is_visible.conformation_popup_text');
            } else {
                alertText = this.$t('ticket.is_invisible.conformation_popup_text');
            }
            this.$swal({
                title: this.$t('ticket.visible.conformation_popup_title'),
                text: alertText,
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'btn-desino',
                },
            }).then(async (result) => {
                if (result.isConfirmed) {
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
                        this.tasks[index].project = this.previousProject;
                    }
                }
            })
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
            this.filter = {
                task_name: "",
                task_type: "",
                action_owner: "",
                next_action_owner: "",
                functionalities: [],
                projects: [],
                macro_status: [],
                is_open_task: false,
                is_include_done: true
            }
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
        }
    },
    mounted() {
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
