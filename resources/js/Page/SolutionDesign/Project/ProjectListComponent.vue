<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('project.list.page_title') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="row mb-3">
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

        <ul class="list-group list-group-flush list mb-3 mt-2">
            <li class="list-group-item font-weight-bold bg-desino text-white rounded-top">
                <div class="row w-100">
                    <div class="col-lg-4 col-md-6 col-6 fw-bold py-2">
                        {{ $t('project.list.name_th_text') }}
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 fw-bold py-2 d-flex justify-content-center align-items-center">
                        {{ $t('project.list.project_status_th_text') }}
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 fw-bold py-2 d-flex justify-content-center align-items-center">
                        {{ $t('project.list.tickets_th_text') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-flex justify-content-end align-items-end">
                        {{ $t('project.list.actions_th_text') }}
                    </div>
                </div>
            </li>
            <li class="list-group-item border" v-if="projects.length > 0" v-for="project in projects">
                <div class="row w-100">
                    <div class="col-lg-4 col-md-6 col-6 py-1">
                        {{ project.name }}
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 py-1 d-flex justify-content-center align-items-center">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                                v-model="project.status" @change="handleCheckboxChange(project)">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-6 py-1 d-flex justify-content-center align-items-center">
                        {{ project.tickets_count }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 py-1 d-flex justify-content-end align-items-end">
                        <a :title="$t('project.list.actions_edit_tooltip')" class="text-desino me-2" href="javascript:"
                            @click="editProject(project)">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                    </div>
                </div>
            </li>
            <li v-else class="list-group-item row border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('project.list.projects_not_found_text') }}
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
import { Modal } from 'bootstrap';
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
                this.setLoading(false);
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
        this.getProjectList();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
};
</script>
