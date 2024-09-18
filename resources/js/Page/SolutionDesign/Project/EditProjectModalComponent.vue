<template>
    <div class="modal-dialog">
        <form @submit.prevent="updateProject">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">{{ $t('project.list.edit.modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div class="row">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-desino bg-desino text-light">{{
                        $t('project.list.edit.modal_submit_but_text') }}</button>
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
                messageService.setMessage(error.message, 'danger');
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