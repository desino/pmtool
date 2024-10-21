<template>
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title font-italic" id="initiativeTimeBookingForAssignProjectLabel">
                    {{ $t('initiative_time_booking_for_assign_project.popup_title') }}
                </h5>
            </div>
            <form @submit.prevent="assignProjectForInitiativeTimeBooking">
                <div class="modal-body">
                    <div class="row w-100">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{
                                $t('initiative_time_booking_for_assign_project.popup_project_select_label_text') }}
                                <strong class="text-danger">*</strong></label>
                            <select v-model="formData.project_id" :class="{ 'is-invalid': errors.project_id }"
                                class="form-select">
                                <option value="">{{
                                    $t('initiative_time_booking_for_assign_project.popup_project_select_placeholder_text')
                                }}</option>
                                <option v-for="project in projects" :key="project.id" :value="project.id">{{
                                    project.name }}
                                </option>
                            </select>
                            <div v-if="errors.project_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.project_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-md-6">
                            <button class="btn btn-desino w-100 border-0" type="submit">
                                {{ $t('initiative_time_booking_for_assign_project.popup_submit_but_text') }}
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-danger w-100 border-0" @click="hideModal" data-bs-dismiss="modal"
                                type="button">
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
import { mapActions, mapGetters } from 'vuex';
import InitiativeTimeBookingService from '../../services/InitiativeTimeBookingService';
import messageService from '../../services/messageService';
import showToast from '../../utils/toasts';
import { Modal } from 'bootstrap';

export default {
    name: 'InitiativeTimeBookingForAssignProjectModalComponent',
    data() {
        return {
            projects: [],
            timeBookingIds: [],
            initiativeId: '',
            formData: {
                initiative_id: '',
                project_id: '',
                time_booking_ids: []
            },
            errors: {}
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getInitialDataForInitiativeTimeBookingsAssignProject(data) {
            this.projects = data.projects;
            this.formData.time_booking_ids = data.time_booking_ids;
            this.timeBookingIds = data.time_booking_ids;
            this.initiativeId = data.initiative_id;
        },
        async assignProjectForInitiativeTimeBooking() {
            this.clearMessages();
            try {
                this.setLoading(true);
                this.formData.time_booking_ids = this.timeBookingIds;
                this.formData.initiative_id = this.initiativeId;
                const { message } = await InitiativeTimeBookingService.assignProjectForInitiativeTimeBookings(this.formData);
                showToast(message, 'success');
                this.hideModal();
                this.setLoading(false);
                this.$emit('pageUpdated');
                this.resetForm();
            } catch (error) {
                this.handleError(error);
            }
        },
        hideModal() {
            const modalElement = document.getElementById('initiativeTimeBookingForAssignProjectModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    this.setLoading(false);
                    modal.hide();
                }
            }
        },
        resetForm() {
            this.formData = {
                initiative_id: '',
                project_id: '',
                time_booking_ids: []
            };
            this.errors = {};
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
    }
}
</script>