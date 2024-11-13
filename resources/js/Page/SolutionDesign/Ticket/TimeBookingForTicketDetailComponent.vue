<template>
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" id="timeBookingForTicketDetailModalLabel">
                    {{ $t('ticket_detail_time_booking_modal.modal_title') }}
                </h5>
            </div>
            <form @submit.prevent="storeTimeBookingForTicketDetail">
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div v-if="showErrorMessage" class="alert alert-danger">
                        <button type="button" class="btn-close" aria-label="Close" @click="hideErrorMessage"></button>
                        {{ showErrorMessage }}
                    </div>
                    <div class="row w-100">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{
                                $t('ticket_detail_time_booking_modal.input_booked_date') }} <strong
                                    class="text-danger">*</strong></label>
                            <Datepicker ref="bookedDateDatePicker" class="form-control" v-model="formData.booked_date"
                                :class="{ 'is-invalid': errors.booked_date }" inputFormat="dd/MM/yyyy"
                                :upper-limit="todayDate" @closed="onBookedDateDatePickerClose()" />
                            <small v-if="errors.booked_date" class="invalid-feedback">
                                <span v-for="(error, index) in errors.booked_date" :key="index">{{ error
                                    }}</span>
                            </small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{
                                $t('ticket_detail_time_booking_modal.input_hours') }} <strong
                                    class="text-danger">*</strong></label>
                            <input type="text" v-model="formData.hours" :class="{ 'is-invalid': errors.hours }"
                                class="form-control" ref="hoursInput">
                            <small v-if="errors.hours" class="invalid-feedback">
                                <span v-for="(error, index) in errors.hours" :key="index">{{ error
                                    }}</span>
                            </small>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold">{{
                                $t('ticket_detail_time_booking_modal.textarea_comments') }} </label>
                            <textarea class="form-control" rows="3" v-model="formData.comments"
                                :class="{ 'is-invalid': errors.comments }" maxlength="500"></textarea>
                            <div v-if="errors.comments" class="invalid-feedback">
                                <span v-for="(error, index) in errors.comments" :key="index">{{ error
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col-12 col-md-12 col-lg-4">
                            <button type="submit" class="btn btn-desino w-100"
                                @click="handleSubmitButtonClickForTicketDetailTimeBooking('create')">{{
                                    $t('ticket_detail_time_booking_modal.submit_but_text') }}</button>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4">
                            <button type="submit" class="btn btn-desino w-100"
                                @click="handleSubmitButtonClickForTicketDetailTimeBooking('create_close')">{{
                                    $t('ticket_detail_time_booking_modal.submit_and_close_but_text')
                                }}</button>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4">
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

import { mapActions } from 'vuex';
import TimeBookingService from '../../../services/TimeBookingService';
import { Modal } from 'bootstrap';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import messageService from '../../../services/messageService';
import Datepicker from 'vue3-datepicker';
import showToast from '../../../utils/toasts';

export default {
    name: 'TimeBookingModalComponent',
    components: {
        GlobalMessage,
        Datepicker
    },
    data() {
        return {
            todayDate: new Date(),
            formData: {
                initiative_id: '',
                ticket_id: '',
                hours: '',
                comments: '',
                booked_date: new Date(),
            },
            submitButtonClickedValue: '',
            showErrorMessage: "",
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        getTimeBookingForTicketDetailData(ticketData) {
            this.clearMessages();
            this.formData.initiative_id = ticketData.initiative_id;
            this.formData.ticket_id = ticketData.id;
        },
        handleSubmitButtonClickForTicketDetailTimeBooking(buttonValue) {
            this.submitButtonClickedValue = buttonValue;
        },
        async storeTimeBookingForTicketDetail() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const { message } = await TimeBookingService.storeTimeBookingForTicketDetail(this.formData);
                showToast(message, 'success');
                this.clearFormData();
                if (this.submitButtonClickedValue === 'create_close') {
                    this.hideModal();
                }
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        onBookedDateDatePickerClose() {
            const hoursInputElement = this.$refs.hoursInput;
            if (hoursInputElement) {
                hoursInputElement.focus();
            }
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                // messageService.setMessage(error.message, 'danger');
                this.showErrorMessage = error.message
            }
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            this.showErrorMessage = "";
            messageService.clearMessage();
        },
        clearFormData() {
            this.formData.hours = '';
            this.formData.comments = '';
            this.formData.booked_date = new Date();
        },
        hideModal() {
            const modalElement = document.getElementById('timeBookingForTicketDetailModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        },
        hideErrorMessage() {
            this.showErrorMessage = "";
        }
    },
    mounted() {
        this.clearMessages();
    },
}
</script>
<style scoped>
.alert {
    margin-top: 1rem;
}

.btn-close {
    position: absolute;
    right: 1rem;
}
</style>