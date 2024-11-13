<template>
    <div class="modal-dialog">
        <form @submit.prevent="submitAssignProject">
            <div class="modal-content border-0">
                <div class="modal-header modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 id="createTicketModalLabel" class="modal-title">{{ $t('ticket.assign.project.modal_title') }}
                    </h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div class="mb-3 p-3 shadow">
                        <select v-model="formData.project_id" :class="{ 'is-invalid': errors.project_id }"
                            id="project_id" class="form-select">
                            <option value="">{{ $t('ticket.assign.project.modal_select_project_name_placeholder') }}
                            </option>
                            <option v-for="project in projectList" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                        <div v-if="errors.project_id" class="invalid-feedback">
                            <span v-for="(error, index) in errors.project_id" :key="index">{{ error }}</span>
                        </div>
                    </div>
                    <div class="row w-100 my-3">
                        <div class="col-5">
                            <hr>
                        </div>
                        <div class="col-2 text-center">
                            <span>OR</span>
                        </div>
                        <div class="col-5">
                            <hr>
                        </div>
                    </div>
                    <div class="mb-3 p-3 shadow">
                        <input id="project_name" v-model="formData.project_name"
                            :class="{ 'is-invalid': errors.project_name }" class="form-control" type="text"
                            :placeholder="$t('ticket.assign.project.modal_input_name_placeholder')">
                        <div v-if="errors.project_name" class="invalid-feedback">
                            <span v-for="(error, index) in errors.project_name" :key="index">{{ error }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-4 col-md-6 col-lg-6">
                            <button class="btn btn-desino w-100 border-0" type="submit">
                                {{ $t('ticket.assign.project.modal_submit_but_text') }}
                            </button>
                        </div>
                        <div class="col-4 col-md-6 col-lg-6">
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
import GlobalMessage from "../../../components/GlobalMessage.vue";
import { mapActions } from "vuex";
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import messageService from '../../../services/messageService';
import TicketService from '../../../services/TicketService';
export default {
    name: 'AssignProjectModalComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
    },
    data() {
        return {
            formData: {
                project_id: '',
                project_name: '',
                initiative_id: '',
                selectedTasks: [],
            },
            projectList: [],
            errors: {},
            showMessage: true
        }
    },
    watch: {
        'formData.project_id'(newVal) {
            if (newVal) {
                this.formData.project_name = '';
            }
        },
        'formData.project_name'(newVal) {
            if (newVal) {
                this.formData.project_id = "";
            }
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        getSelectedTasksData(data) {
            this.formData.selectedTasks = data.tasks;
            this.formData.initiative_id = data.initiative_id;
            this.getProjectList();
        },
        async getProjectList() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const { content } = await TicketService.getInitiativeProjects(this.formData.initiative_id);
                this.projectList = content;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async submitAssignProject() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await TicketService.assignProject(this.formData);
                showToast(response.message, 'success');
                this.hideModal();
                this.setLoading(false);
                this.resetForm();
                this.$emit('refreshTickets');
            } catch (error) {
                this.handleError(error);
            }
        },
        hideModal() {
            const modalElement = document.getElementById('assignProjectModal');
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
        resetForm() {
            this.formData = {
                project_id: '',
                project_name: '',
                initiative_id: '',
                selectedTasks: [],
            };
            this.errors = {};
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