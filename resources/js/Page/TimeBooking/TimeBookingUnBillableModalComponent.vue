<template>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" id="timeBookingOnNewTicketModalLabel"
                    v-html="formattedModalTitleForNewTicket()">
                </h5>
            </div>
            <div class="modal-body">
                <GlobalMessage v-if="showMessage" />
                <div v-if="showErrorMessage" class="alert alert-danger">
                    <button type="button" class="btn-close" aria-label="Close" @click="hideErrorMessage"></button>
                    {{ showErrorMessage }}
                </div>
                <form @submit.prevent="storeTimeBookingForUnBillable">
                    <div class="mb-3 p-3 shadow">
                        <div class="row w-100">
                            <div class="col-6 mb-3">
                                <select v-model="formData.project_id" :class="{ 'is-invalid': errors.project_id }"
                                    class="form-select">
                                    <option value="">{{ $t('time_booking_un_billable.modal_select_project_label_text')
                                        }}</option>
                                    <option v-for="project in projects" :key="project.project_id"
                                        :value="project.project_id">{{
                                            project.project_name }}
                                    </option>
                                </select>
                                <div v-if="errors.project_id" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.project_id" :key="index">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <input type="text" v-model="formData.hours" :class="{ 'is-invalid': errors.hours }"
                                    class="form-control"
                                    :placeholder="$t('time_booking_un_billable.modal_input_hours_label_text')">
                                <small v-if="errors.hours" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.hours" :key="index">{{ error
                                        }}</span>
                                </small>
                            </div>
                        </div>
                        <div class="row w-100">
                            <div class="col-12 mb-3">
                                <textarea class="form-control" rows="3" v-model="formData.comments"
                                    :class="{ 'is-invalid': errors.comments }"
                                    :placeholder="$t('time_booking_un_billable.modal_textarea_comments_label_text')"
                                    maxlength="500"></textarea>
                                <div v-if="errors.comments" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.comments" :key="index">{{ error
                                        }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row w-100">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-desino w-100"
                                    @click="handleSubmitButtonClickForUnBillable('create')">{{
                                        $t('time_booking_un_billable.modal_submit_but_text') }}</button>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-desino w-100"
                                    @click="handleSubmitButtonClickForUnBillable('create_close')">{{
                                        $t('time_booking_un_billable.modal_submit_and_close_but_text')
                                    }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mb-3 p-3 shadow">
                    <div class="mb-3 p-3 shadow">
                        <button type="button" class="btn btn-sm btn-danger border-0"
                            :disabled="selectedTimeBookings.length === 0" @click="handleDeleteSelectAllTimeBookings">
                            <i class="bi bi-trash3"></i> {{ $t('time_booking_un_billable.modal_delete_but_text') }}
                        </button>
                        <ul class="list-group list-group-flush mb-3 mt-2">
                            <li class="font-weight-bold bg-desino text-white rounded-top list-group-item">
                                <div class="row w-100">
                                    <div class="col-md-1 fw-bold py-2">
                                        <input class="form-check-input" type="checkbox" v-model="isChkAllTimeBookings"
                                            @change="handleSelectAllTimeBookings">
                                    </div>
                                    <div class="col-md-2 fw-bold py-2">
                                        {{ $t('time_booking_un_billable.modal.list_table.hours') }}
                                    </div>
                                    <div class="col-md-2 fw-bold py-2">
                                        {{ $t('time_booking_un_billable.modal.list_table.project_name') }}
                                    </div>
                                    <div class="col-md-6 fw-bold py-2">
                                        {{ $t('time_booking_un_billable.modal.list_table.comments') }}
                                    </div>
                                    <div class="col-md-1 fw-bold py-2 text-end">
                                        {{ $t('time_booking_un_billable.modal.list_table.action') }}
                                    </div>
                                </div>
                            </li>
                            <li class="border list-group-item" v-if="timeBookings.length > 0"
                                v-for="timeBooking in timeBookings">
                                <div class="row w-100">
                                    <div class="col-md-1 py-2">
                                        <input v-model="timeBooking.is_checked" class="form-check-input" type="checkbox"
                                            @change="handleSelectAllTimeBooking(timeBooking)">
                                    </div>
                                    <div class="col-md-2 py-2">
                                        {{ timeBooking.hours }}
                                    </div>
                                    <div class="col-md-2 fw-bold py-2">
                                        {{ timeBooking.project_name }}
                                    </div>
                                    <div class="col-md-6 py-2">
                                        {{ timeBooking.comments }}
                                    </div>
                                    <div class="col-md-1 py-2 text-end">
                                        <a :title="$t('time_booking_un_billable.modal.list_table.action_delete_text')"
                                            class="text-danger me-2" href="javascript:"
                                            @click="handleDeleteTimeBooking(timeBooking)">
                                            <i class="bi bi-trash3"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                            <li class="border list-group-item" v-if="totalTimeBookingHours > 0">
                                <div class="row w-100">
                                    <div class="col-md-1 py-2 fw-bold">
                                        {{ $t('time_booking_un_billable.modal.list_table.footer_total_text') }}
                                    </div>
                                    <div class="col-md-2 py-2 fw-bold">
                                        {{ totalTimeBookingHours }}
                                    </div>
                                    <div class="col-md-6 py-2 fw-bold">
                                    </div>
                                    <div class="col-md-3 py-2">
                                    </div>
                                </div>
                            </li>
                            <li class="border list-group-item" v-if="timeBookings.length === 0">
                                <div class="row w-100">
                                    <div class="col-md-12 py-2 fw-bold text-center">
                                        {{ $t('time_booking_un_billable.modal.list_table.no_data_text') }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-12 col-md-12 col-lg-12">
                        <button class="btn btn-danger w-100 border-0" data-bs-dismiss="modal" type="button">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
import { Modal } from 'bootstrap';
import messageService from '../../services/messageService';
import GlobalMessage from '../../components/GlobalMessage.vue';
import showToast from '../../utils/toasts';
import TimeBookingService from '../../services/TimeBookingService';

export default {
    name: 'TimeBookingUnBillableModalComponent',
    components: {
        GlobalMessage
    },
    data() {
        return {
            formData: {
                initiative_id: '',
                project_id: '',
                hours: '',
                comments: '',
                booked_date: '',
                project_id: ''
            },
            weekDay: {},
            weekDays: [],
            projects: [],
            timeBookings: [],
            submitButtonClickedValue: '',
            selectedTimeBookings: [],
            isChkAllTimeBookings: false,
            totalTimeBookingHours: 0,
            showErrorMessage: "",
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        getTimeBookingData(timeBooking, weekDay, weekDays, projects) {
            this.clearFormData();
            this.clearMessages();
            this.formData.initiative_id = timeBooking.initiative_id;
            this.formData.booked_date = weekDay.date;
            this.weekDays = weekDays;
            this.weekDay = weekDay;
            this.projects = projects;
            this.getTimeBookingUnBillableModalInitialData();
        },
        async getTimeBookingUnBillableModalInitialData() {
            this.setLoading(true);
            try {
                const passData = {
                    initiative_id: this.formData.initiative_id,
                    booked_date: this.formData.booked_date,
                }
                const { content: { timeBookings, totalTimeBookingHours } } = await TimeBookingService.getTimeBookingUnBillableModalInitialData(passData);
                this.timeBookings = timeBookings;
                this.totalTimeBookingHours = totalTimeBookingHours;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        formattedModalTitleForNewTicket() {
            const title = this.$t('time_booking_un_billable.popup_title', { DATE: this.weekDay?.format_date_dd_mm_yyyy });
            return title.replace(this.weekDay?.format_date_dd_mm_yyyy, `<span class='badge bg-secondary'>${this.weekDay?.format_date_dd_mm_yyyy}</span>`);
        },
        async storeTimeBookingForUnBillable() {
            this.setLoading(true);
            this.clearMessages();
            try {
                const { message } = await TimeBookingService.storeTimeBookingForUnBillable(this.formData);
                showToast(message, 'success');
                this.$emit('pageUpdated', this.weekDays);
                if (this.submitButtonClickedValue == 'create_close') {
                    this.hideModal();
                }
                this.showMessage = true;
                this.setLoading(false);
                this.getTimeBookingUnBillableModalInitialData();
                this.formData.project_id = '';
                this.formData.hours = '';
                this.formData.comments = '';
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSubmitButtonClickForUnBillable(submitButtonClickedValue) {
            this.submitButtonClickedValue = submitButtonClickedValue;
        },
        handleSelectAllTimeBookings() {
            this.selectedTimeBookings = [];
            this.timeBookings = this.timeBookings.map(timeBooking => {
                timeBooking.is_checked = this.isChkAllTimeBookings;
                if (this.isChkAllTimeBookings) {
                    this.selectedTimeBookings.push(timeBooking.id);
                }
                return timeBooking;
            });
        },
        handleSelectAllTimeBooking(timeBooking) {
            if (timeBooking.is_checked) {
                this.selectedTimeBookings.push(timeBooking.id);
            } else {
                const index = this.selectedTimeBookings.indexOf(timeBooking.id);
                if (index > -1) {
                    this.selectedTimeBookings.splice(index, 1);
                }
            }
            this.isChkAllTimeBookings = this.timeBookings.every(timeBooking => timeBooking.is_checked);
        },
        handleDeleteSelectAllTimeBookings() {
            if (this.selectedTimeBookings.length === 0) {
                return false;
            }
            this.deleteTimeBooking(this.selectedTimeBookings, 'deleteAll');
        },
        handleDeleteTimeBooking(timeBooking) {
            this.deleteTimeBooking([timeBooking.id], 'deleteOne');
        },
        async deleteTimeBooking(timeBookingIds = [], action = '') {
            this.clearMessages();
            this.$swal({
                title: this.$t('time_booking_un_billable.modal.delete_alert.title'),
                text: this.$t('time_booking_un_billable.modal.delete_alert.text'),
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
                    this.setLoading(true);
                    try {
                        const passData = {
                            'timeBookingIds': timeBookingIds
                        }
                        const { message } = await TimeBookingService.deleteTimeBookings(passData);
                        showToast(message, 'success');
                        this.$emit('pageUpdated', this.weekDays);
                        this.showMessage = true;
                        this.setLoading(false);
                        this.getTimeBookingUnBillableModalInitialData();
                        if (action === 'deleteAll') {
                            this.isChkAllTimeBookings = false;
                            this.selectedTimeBookings = [];
                        }
                    } catch (error) {
                        this.handleError(error);
                    }
                } else {

                }
            }).catch(() => {

            });
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
            this.formData = {
                initiative_id: '',
                project_id: '',
                hours: '',
                comments: '',
                booked_date: '',
            };
        },
        hideModal() {
            const modalElement = document.getElementById('timeBookingUnBillableModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        },
        hideErrorMessage() {
            this.showErrorMessage = "";
        },
    },
    mounted() {
        this.clearMessages();
    },
};
</script>