<template>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInitiativeModalLabel">{{ $t('time_booking.popup_title') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <GlobalMessage v-if="showMessage" />
                <div v-if="showErrorMessage" class="alert alert-danger">
                    <button type="button" class="btn-close" aria-label="Close" @click="hideErrorMessage"></button>
                    {{ showErrorMessage }}
                </div>
                <form @submit.prevent="storeTimeBooking">
                    <div class="mb-3 p-3 shadow">
                        <div class="row w-100">
                            <div class="col-2 mb-3">
                                <input type="text" v-model="formData.hours" :class="{ 'is-invalid': errors.hours }"
                                    class="form-control" :placeholder="$t('time_booking.modal_input_hours_label_text')">
                                <small v-if="errors.hours" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.hours" :key="index">{{ error
                                        }}</span>
                                </small>
                            </div>
                            <div class="col-10 mb-3">
                                <textarea class="form-control" rows="3" v-model="formData.comments"
                                    :class="{ 'is-invalid': errors.comments }"
                                    :placeholder="$t('time_booking.modal_textarea_Comments_label_text')"
                                    maxlength="500"></textarea>
                                <div v-if="errors.comments" class="invalid-feedback">
                                    <span v-for="(error, index) in errors.comments" :key="index">{{ error
                                        }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-desino w-100">{{
                                    $t('time_booking.modal_submit_but_text') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="mb-3 p-3 shadow">
                    <button type="button" class="btn btn-sm btn-danger border-0"
                        :disabled="selectedTimeBookings.length === 0" @click="handleDeleteSelectAllTimeBookings">
                        <i class="bi bi-trash3"></i> {{ $t('time_booking.modal_delete_but_text') }}
                    </button>
                    <ul class="list-group list-group-flush mb-3 mt-2">
                        <li class="font-weight-bold bg-desino text-white rounded-top list-group-item">
                            <div class="row w-100">
                                <div class="col-md-1 fw-bold py-2">
                                    <input class="form-check-input" type="checkbox" v-model="isChkAllTimeBookings"
                                        @change="handleSelectAllTimeBookings">
                                </div>
                                <div class="col-md-2 fw-bold py-2">
                                    {{ $t('time_booking.modal.list_table.hours') }}
                                </div>
                                <div class="col-md-6 fw-bold py-2">
                                    {{ $t('time_booking.modal.list_table.comments') }}
                                </div>
                                <div class="col-md-3 fw-bold py-2 text-end">
                                    {{ $t('time_booking.modal.list_table.action') }}
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
                                <div class="col-md-6 py-2">
                                    {{ timeBooking.comments }}
                                </div>
                                <div class="col-md-3 py-2 text-end">
                                    <a :title="$t('time_booking.modal.list_table.action_delete_text')"
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
                                    {{ $t('time_booking.modal.list_table.footer_total_text') }}
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
                                    {{ $t('time_booking.modal.list_table.no_data_text') }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</template>
<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import messageService from '../../services/messageService';
import { mapActions } from 'vuex';
import TimeBookingService from '../../services/TimeBookingService';
import { Modal } from 'bootstrap';

export default {
    name: 'TimeBookingModalComponent',
    components: {
        GlobalMessage,
    },
    data() {
        return {
            formData: {
                initiative_id: '',
                ticket_id: '',
                hours: '',
                comments: '',
                booked_date: '',
            },
            timeBookings: [],
            totalTimeBookingHours: 0,
            isChkAllTimeBookings: false,
            selectedTimeBookings: [],
            showErrorMessage: "",
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        getTimeBookingData(timeBooking, weekDay, ticket = {}) {
            this.clearFormData();
            this.formData.initiative_id = timeBooking.initiative_id;
            this.formData.ticket_id = ticket?.ticket_id ?? null;
            this.formData.booked_date = weekDay.date;
            this.getTimeBookingModalInitialData();
        },
        async storeTimeBooking() {
            this.setLoading(true);
            this.clearMessages();
            try {
                const { content } = await TimeBookingService.storeTimeBooking(this.formData);
                this.$emit('pageUpdated');
                // this.hideModal();
                this.showMessage = true;
                this.setLoading(false);
                this.getTimeBookingModalInitialData();
                this.formData.hours = '';
                this.formData.comments = '';
            } catch (error) {
                this.handleError(error);
            }
        },
        async getTimeBookingModalInitialData() {
            this.setLoading(true);
            try {
                const passData = {
                    'initiative_id': this.formData.initiative_id,
                    'ticket_id': this.formData.ticket_id,
                    'booked_date': this.formData.booked_date
                }
                const { content: { timeBookings, totalTimeBookingHours } } = await TimeBookingService.getTimeBookingModalInitialData(passData);
                this.timeBookings = timeBookings;
                this.totalTimeBookingHours = totalTimeBookingHours;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
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
                title: this.$t('time_booking.modal.delete_alert.title'),
                text: this.$t('time_booking.modal.delete_alert.text'),
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
                        const { content } = await TimeBookingService.deleteTimeBookings(passData);
                        this.$emit('pageUpdated');
                        this.showMessage = true;
                        this.setLoading(false);
                        this.getTimeBookingModalInitialData();
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
            messageService.clearMessage();
        },
        clearFormData() {
            this.formData = {
                initiative_id: '',
                ticket_id: '',
                hours: '',
                comments: '',
                booked_date: '',
            };
        },
        hideModal() {
            const modalElement = document.getElementById('timeBookingModal');
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