<template>
    <div class="modal-dialog">
        <form @submit.prevent="updateProject">
            <div class="modal-content border-0">
                <div class="modal-header modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="editProjectModalLabel">{{ $t('project.list.edit.modal_title') }}</h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <div class="row w-100">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    {{ $t('project.list.edit.modal.form.project_name') }}
                                    <strong class="text-danger">*</strong>
                                </label>
                                <input type="text" v-model="formData.name" :class="{ 'is-invalid': errors.name }"
                                    class="form-control">
                                <div v-if="errors.name" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.name" :key="index">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-4 col-md-6 col-lg-6">
                            <button type="submit" class="btn btn-desino w-100 border-0">{{
                                $t('project.list.edit.modal_submit_but_text') }}</button>
                        </div>
                        <div class="col-4 col-md-6 col-lg-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
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
import GlobalMessage from './../../../components/GlobalMessage.vue';
import showToast from './../../../utils/toasts';
import ProjectService from './../../../services/ProjectService';
import { mapActions } from 'vuex';
import { Modal } from 'bootstrap';
import messageService from '../../../services/messageService';
export default {
    name: 'ProjectEditModalComponent',
    components: {
        GlobalMessage,
    },
    data() {
        return {
            formData: {
                id: '',
                initiative_id: '',
                name: '',
            },
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        getEditProjectFormData(project) {
            this.formData.id = project.id;
            this.formData.initiative_id = project.initiative_id;
            this.formData.name = project.name;
        },
        async updateProject() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const response = await ProjectService.updateProject(this.formData);
                showToast(response.message, 'success');
                this.$emit('projectUpdated');
                this.hideModal();
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
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
            messageService.clearMessage();
        },
        hideModal() {
            const modalElement = document.getElementById('editProjectModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        }
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>