<template>
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" id="planNewInitiativeModalLabel">
                    {{ $t('planning.plan_new_project_modal_title') }}
                </h5>
            </div>
            <form @submit.prevent="addPlanNewProject">
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <div class="row w-100">
                        <div class="col-12 mb-3">
                            <select v-model="formData.project_id" :class="{ 'is-invalid': errors.project_id }"
                                class="form-select" @change="fetchUsers">
                                <option value="">{{
                                    $t('planning.plan_new_project.modal_select_project_label_text')
                                    }}</option>
                                <option v-for="project in projectList" :key="project.id" :value="project.id">{{
                                    project.name }}
                                </option>
                            </select>
                            <div v-if="errors.project_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.project_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <select v-model="formData.user_id" :class="{ 'is-invalid': errors.user_id }"
                                class="form-select">
                                <option value="">{{
                                    $t('planning.plan_new_project.modal_select_user_label_text')
                                    }}</option>
                                <option v-for="user in usersList" :key="user.id" :value="user.id">{{
                                    user.name }}
                                </option>
                            </select>
                            <div v-if="errors.user_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.user_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-12 col-md-12 col-lg-6">
                            <button type="submit" class="btn btn-desino w-100" :disabled="!isDisableSubmitBut">{{
                                $t('planning.plan_new_project.modal_submit_but_text') }}</button>
                        </div>
                        <div class="col-12 col-md-12 col-lg-6">
                            <button class="btn btn-danger w-100 border-0" data-bs-dismiss="modal" type="button">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import { mapActions } from 'vuex';
import { Modal } from 'bootstrap';
import messageService from '../../services/messageService';
import PlanningService from '../../services/PlanningService';

export default {
    name: 'PlanNewProjectModalComponent',
    components: {
        GlobalMessage
    },
    computed: {
        isDisableSubmitBut() {
            return this.formData.project_id && this.formData.user_id;
        }
    },
    data() {
        return {
            formData: {
                initiative_id: '',
                project_id: '',
                user_id: ''
            },
            planning: {},
            projectList: [],
            usersList: [],
            existingPlanningProjectIds: [],
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getPlanNewProjectForOpenModalData(existingProjects, planning) {
            this.clearForm();
            this.planning = planning;
            this.formData.initiative_id = planning.initiative_id;
            this.existingPlanningProjectIds = existingProjects.map(item => item.project_id);
            try {
                const { content: { initiatives, projects, users } } = await PlanningService.getPlanningInitialData();
                this.projectList = projects.filter(item => !this.existingPlanningProjectIds.includes(item.id) && item.initiative_id == planning.initiative_id);
                this.usersList = users;
            } catch (error) {
                this.handleError(error);
            }
        },
        addPlanNewProject() {
            if (!this.formData.user_id) {
                return;
            }
            this.formData.project_name = this.projectList.find(item => item.id == this.formData.project_id).name;
            this.formData.user_name = this.usersList.find(item => item.id == this.formData.user_id).name;
            this.$emit('add-plan-new-project', this.formData);
            this.hideModal();
        },
        hideModal() {
            const modalElement = document.getElementById('planNewProjectModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
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
            messageService.clearMessage('modal');
        },
        clearForm() {
            this.formData.project_id = '';
            this.formData.user_id = '';
        }
    },
    mounted() {
        this.clearMessages();
        this.clearForm();
    }
}
</script>