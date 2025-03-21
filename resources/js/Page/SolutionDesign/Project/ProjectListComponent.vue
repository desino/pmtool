<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content my-3">
        <div class="row g-1 align-items-center mb-3">
            <div class="col-12 col-md-3 col-lg-3">
                <div class="w-100 p-1">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inline_active" v-model="filter.active"
                            @change="getProjectList">
                        <label class="form-check-label fw-bold" for="inline_active">Active</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inline_inactive" v-model="filter.inactive"
                            @change="getProjectList">
                        <label class="form-check-label fw-bold" for="inline_inactive">Inactive</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-7 col-md-7 col-5">
                            {{ $t('project.list.name_th_text') }}
                        </div>
                        <div class="col-lg-2 col-md-2 col-3">
                            {{ $t('project.list.project_status_th_text') }}
                        </div>
                        <div class="col-lg-2 col-md-2 col-3">
                            {{ $t('project.list.tickets_th_text') }}
                        </div>
                        <div class="col-lg-1 col-md-1 col-1 text-end"></div>
                    </div>
                </li>
                <li class="list-group-item p-1 list-group-item-action" v-if="projects.length > 0" v-for="project in projects">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-lg-7 col-md-7 col-5">
                            {{ project.name }}
                        </div>
                        <div class="col-lg-2 col-md-2 col-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="flexSwitchCheckChecked" v-model="project.status"
                                    @change="showConfirmation('handleCheckboxChange', handleCheckboxChange, project)">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-3">
                            {{ project.tickets_count }}
                        </div>
                        <div class="col-lg-1 col-md-1 col-1 text-end">
                            <div class="dropdown">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="editProject(project)" >
                                            {{ $t('project.list.actions_edit_project') }}
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
                            {{ $t('project.list.projects_not_found_text') }}
                        </div>
                    </div>
                </li>
            </ul>
            <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
                @page-changed="getProjectList" />
        </div>

        <div id="editProjectModal" aria-hidden="true" aria-labelledby="editProjectModalLabel" class="modal fade"
            tabindex="-1">
            <EditProjectModalComponent ref="editProjectModalComponent" @projectUpdated="getProjectList" />
        </div>
        <ConfirmationModal ref="dynamicConfirmationModal" :title="modalTitle" :message="modalMessage"
            @confirm="modalConfirmCallback" />
    </div>
</template>
<script>
import messageService from '../../../services/messageService';
import ProjectService from '../../../services/ProjectService';
import GlobalMessage from './../../../components/GlobalMessage.vue';
import { mapActions } from 'vuex';
import PaginationComponent from './../../../components/PaginationComponent.vue';
import showToast from './../../../utils/toasts';
import EditProjectModalComponent from './EditProjectModalComponent.vue';
import { Modal, Tooltip } from 'bootstrap';
import store from '../../../store';
import eventBus from '../../../eventBus';
export default {
    name: 'ProjectList',
    components: {
        GlobalMessage,
        PaginationComponent,
        EditProjectModalComponent
    },
    data() {
        return {
            initiative_id: this.$route.params.id,
            projects: [],
            currentPage: "",
            totalPages: "",
            filter: {
                active: true,
                inactive: false
            },
            modalTitle: '',
            modalMessage: '',
            modalConfirmCallback: null,
            initiativeData: {},
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getProjectList(page = 1) {
            this.clearMessages();
            try {
                const params = {
                    page: page,
                    filters: this.filter,
                    initiative_id: this.initiative_id
                }
                await this.setLoading(true);
                const { content: { projects: { records, paginationInfo: { current_page: currentPage, last_page: totalPages } } }, meta_data: { initiative } } = await ProjectService.getProjects(params);
                this.projects = records.map(item => ({
                    ...item,
                    status: Boolean(item.status),
                    originalStatus: Boolean(item.status)
                }));
                this.currentPage = currentPage;
                this.totalPages = totalPages;
                this.initiativeData = initiative;
                await this.setLoading(false);
                this.initializeTooltips();
                this.setPageHeader();
            } catch (error) {
                this.handleError(error);
            }
        },
        async handleCheckboxChange(project) {
            try {
                project.initiative_id = this.initiative_id;
                const { message } = await ProjectService.updateProjectStatus(project);
                showToast(message, 'success');
                this.getProjectList();
            } catch (error) {
                project.status = !project.status;
                this.handleError(error);
            }
        },
        async editProject(project) {
            this.$refs.editProjectModalComponent.getEditProjectFormData(project);
            const modalElement = document.getElementById('editProjectModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        showConfirmation(modalType, callback, callbackParam) {
            if (modalType === 'handleCheckboxChange') {
                this.modalTitle = this.$t('project.list.actions_change_status_modal_title_text');
                this.modalMessage = this.$t('project.list.actions_change_status_modal_text');
            }

            this.modalConfirmCallback = () => callback(callbackParam);

            this.$refs.dynamicConfirmationModal.showModal();

            const modalElement = document.getElementById('confirmationModal');
            modalElement.addEventListener('hidden.bs.modal', () => {
                if (!this.$refs.dynamicConfirmationModal.isConfirmed) {
                    this.resetSwitchValue(callbackParam);
                }
            }, { once: true });
        },
        resetSwitchValue(project) {
            project.status = Boolean(project.originalStatus);
        },
        setPageHeader() {
            const setHeaderData = {
                page_title: this.$t('project.list.page_title') + ' - ' + this.initiativeData?.name,
            }
            store.commit("setHeaderData", setHeaderData);
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
        eventBus.$emit('selectHeaderInitiativeId', this.initiative_id);
        this.clearMessages();
        this.getProjectList();
    },
    beforeUnmount() {
        this.showMessage = false;
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.getProjectList();
        next();
    },
};
</script>
