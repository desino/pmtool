<template>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" id="timeBookingOnNewTicketModalLabel"
                    v-html="formattedModalTitleForNewTicket()">
                </h5>
            </div>
            <form @submit.prevent="storeTimeBookingOnNewTicket">
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <div v-if="showErrorMessage" class="alert alert-danger">
                        <button type="button" class="btn-close" aria-label="Close" @click="hideErrorMessage"></button>
                        {{ showErrorMessage }}
                    </div>
                    <div class="row w-100">
                        <div class="col-6 mb-3">
                            <select v-model="formData.ticket_id" :class="{ 'is-invalid': errors.ticket_id }"
                                class="form-select">
                                <option value="">{{ $t('time_booking_on_new_ticket.modal_select_ticket_label_text')
                                    }}</option>
                                <option v-for="ticket in tickets" :key="ticket.id" :value="ticket.id">{{
                                    ticket.composed_name }}
                                </option>
                            </select>
                            <div v-if="errors.ticket_id" class="invalid-feedback">
                                <span v-for="(error, index) in errors.ticket_id" :key="index">{{ error }}</span>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <input type="text" v-model="formData.hours" :class="{ 'is-invalid': errors.hours }"
                                class="form-control"
                                :placeholder="$t('time_booking_on_new_ticket.modal_input_hours_label_text')">
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
                                :placeholder="$t('time_booking_on_new_ticket.modal_textarea_comments_label_text')"
                                maxlength="500"></textarea>
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
                                @click="handleSubmitButtonClickForNewTicket('create')">{{
                                    $t('time_booking_on_new_ticket.modal_submit_but_text') }}</button>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4">
                            <button type="submit" class="btn btn-desino w-100"
                                @click="handleSubmitButtonClickForNewTicket('create_close')">{{
                                    $t('time_booking_on_new_ticket.modal_submit_and_close_but_text')
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
            weekDay: {},
            tickets: [],
            weekDays: [],
            submitButtonClickedValue: '',
            showErrorMessage: "",
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        getTimeBookingData(timeBooking, weekDay, weekDays) {
            this.clearFormData();
            this.clearMessages();
            this.formData.initiative_id = timeBooking.initiative_id;
            this.formData.booked_date = weekDay.date;
            this.weekDays = weekDays;
            this.weekDay = weekDay;
            this.getTimeBookingOnNewTicketModalInitialData();
        },
        formattedModalTitleForNewTicket() {
            const title = this.$t('time_booking_on_new_ticket.popup_title', { DATE: this.weekDay?.format_date_dd_mm_yyyy });
            return title.replace(this.weekDay?.format_date_dd_mm_yyyy, `<span class='badge bg-secondary'>${this.weekDay?.format_date_dd_mm_yyyy}</span>`);
        },
        async getTimeBookingOnNewTicketModalInitialData() {
            this.setLoading(true);
            try {
                const passData = {
                    initiative_id: this.formData.initiative_id,
                    booked_date: this.formData.booked_date,
                    start_date: this.weekDays[0]?.date,
                    end_date: this.weekDays[this.weekDays.length - 1]?.date
                }
                const { content: { tickets } } = await TimeBookingService.getTimeBookingOnNewTicketModalInitialData(passData);
                this.tickets = tickets;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async storeTimeBookingOnNewTicket() {
            this.setLoading(true);
            this.clearMessages();
            try {
                const { content } = await TimeBookingService.storeTimeBookingOnNewTicket(this.formData);
                this.$emit('pageUpdated', this.weekDays);
                this.getTimeBookingOnNewTicketModalInitialData();
                if (this.submitButtonClickedValue == 'create_close') {
                    this.hideModal();
                }
                this.showMessage = true;
                this.setLoading(false);
                this.formData.ticket_id = '';
                this.formData.hours = '';
                this.formData.comments = '';
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSubmitButtonClickForNewTicket(buttonValue) {
            this.submitButtonClickedValue = buttonValue;
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
                ticket_id: '',
                hours: '',
                comments: '',
                booked_date: '',
            };
        },
        hideModal() {
            const modalElement = document.getElementById('timeBookingOnNewTicketModal');
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