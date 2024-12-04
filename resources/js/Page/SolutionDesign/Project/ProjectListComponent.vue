<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content my-3">
        <div class="row g-0 w-100 py-2">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inline_active" v-model="filter.active"
                        @change="getProjectList">
                    <label class="form-check-label" for="inline_active">Active</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="inline_inactive" v-model="filter.inactive"
                        @change="getProjectList">
                    <label class="form-check-label" for="inline_inactive">Inactive</label>
                </div>
            </div>
        </div>

        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                <div class="row w-100 align-items-center">
                    <div class="col-lg-5 col-md-6 col-6 fw-bold small">
                        {{ $t('project.list.name_th_text') }}
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 fw-bold small">
                        {{ $t('project.list.project_status_th_text') }}
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 fw-bold small">
                        {{ $t('project.list.tickets_th_text') }}
                    </div>
                    <div class="col-lg-1 col-md-6 col-6 fw-bold small text-end">
                        {{ $t('project.list.actions_th_text') }}
                    </div>
                </div>
            </li>
            <li class="border list-group-item p-1 list-group-item-action border-top-0" v-if="projects.length > 0"
                v-for="project in projects">
                <div class="row w-100 align-items-center">
                    <div class="col-lg-5 col-md-6 col-6">
                        {{ project.name }}
                    </div>
                    <div class="col-lg-3 col-md-6 col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                                v-model="project.status" @change="handleCheckboxChange(project)">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-6">
                        {{ project.tickets_count }}
                    </div>
                    <div class="col-lg-1 col-md-6 col-6 text-end">
                        <a data-bs-toggle="tooltip" data-bs-placement="bottom"
                            :title="$t('project.list.actions_edit_tooltip')" class="link-desino" href="javascript:"
                            @click="editProject(project)">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </div>
                </div>
            </li>
            <li v-else class="border border-top-0 list-group-item px-0 py-1 list-group-item-action">
                <div class="h4 fw-bold text-center">{{ $t('project.list.projects_not_found_text') }}
                </div>
            </li>
        </ul>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="getProjectList" />

        <div id="editProjectModal" aria-hidden="true" aria-labelledby="editProjectModalLabel" class="modal fade"
            tabindex="-1">
            <EditProjectModalComponent ref="editProjectModalComponent" @projectUpdated="getProjectList" />
        </div>
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
                this.setLoading(true);
                const { content: { projects: { records, paginationInfo: { current_page: currentPage, last_page: totalPages } } } } = await ProjectService.getProjects(params);
                this.projects = records.map(item => ({
                    ...item,
                    status: Boolean(item.status)
                }));
                this.currentPage = currentPage;
                this.totalPages = totalPages;
                await this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleCheckboxChange(project) {
            this.$swal({
                title: this.$t('project.list.actions_change_status_modal_title_text'),
                text: this.$t('project.list.actions_change_status_modal_text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'btn-desino',
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        project.initiative_id = this.initiative_id;
                        const { message } = await ProjectService.updateProjectStatus(project);
                        showToast(message, 'success');
                        // this.getProjectList();
                    } catch (error) {
                        project.status = !project.status;
                        this.handleError(error);
                    }
                } else {
                    project.status = !project.status;
                }
            });
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
        const setHeaderData = {
            page_title: this.$t('project.list.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
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
