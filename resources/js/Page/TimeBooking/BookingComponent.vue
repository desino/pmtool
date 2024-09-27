<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('time_booking.page_title') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100">
            <table class="table table-bordered w-100">
                <tbody>
                    <tr class="bg-desino">
                        <th class="bg-transparent text-center text-white align-middle p-1" width="200px">Initiative
                            Name</th>
                        <th class="bg-transparent text-center text-white align-middle p-1" width="300px;">Ticket Name
                        </th>
                        <th class="bg-dark text-center align-middle p-1" width="10px;">
                            <a class="text-white" href="javascript:void(0);" @click="getTimeBookingData(-1)">
                                <i class="bi bi-caret-left"></i>
                            </a>
                        </th>
                        <td class="text-center align-middle p-1"
                            :class="weekDay.is_today ? 'bg-black' : 'bg-transparent'"
                            v-for="(weekDay, index) in weekDays" :key="index" width="60px;">
                            <small class="small text-white" style="font-size: 0.8rem;">
                                {{ weekDay.format_date }}
                            </small>
                        </td>
                        <th class="bg-dark text-center align-middle p-1" width="10px;">
                            <a class="text-white" href="javascript:void(0);" @click="getTimeBookingData(-1)">
                                <i class="bi bi-caret-right"></i>
                            </a>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="bg-opacity-25 bg-primary text-center align-middle p-1">Total</th>
                        <td rowspan="5"></td>
                        <td class="text-center align-middle p-1" v-for="(weekDay, index) in weekDays" :key="index">
                            <small v-if="weekDay.total_hours > 0" class="badge text-white bg-secondary" style="
                                font-size: 0.8rem;">
                                {{ weekDay.total_hours }}
                            </small>
                        </td>
                        <td rowspan="5"></td>
                    </tr>
                    <template v-for="(timeBooking, timeBookingIndex) in timeBookings" :key="timeBookingIndex">
                        <tr v-for="(ticket, ticketIndex) in timeBooking.tickets" :key="ticketIndex">
                            <th class="text-left p-1" :rowspan="timeBooking.tickets.length" v-if="ticketIndex === 0">
                                {{ timeBooking.initiative_name }}
                            </th>
                            <th class="text-left align-middle p-1">
                                <small>{{ ticket.ticket_name }}</small>
                            </th>
                            <td class="text-center align-middle p-1" v-for="(weekDay, index) in weekDays" :key="index">
                                <span class="badge text-secondary">{{ ticket.hours_per_day[weekDay.date] }}</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import GlobalMessage from '../../components/GlobalMessage.vue';
import { mapActions } from 'vuex';
import messageService from '../../services/messageService';
import showToast from '../../utils/toasts';
import eventBus from './../../eventBus';
import TimeBookingService from '../../services/TimeBookingService';
export default {
    name: 'BookingComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage
    },
    data() {
        return {
            timeBookings: [],
            weekDays: [],
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getTimeBookingData(number = 0) {
            this.clearMessages();
            this.setLoading(true);
            try {
                const passData = {
                    previous_or_next_of_week: number,
                    start_date: this.weekDays[0]?.date,
                    end_date: this.weekDays[this.weekDays.length - 1]?.date
                }
                const { content: { weekDays, initiativeWithTicketsAndTimeBooking } } = await TimeBookingService.getTimeBookingData(passData);
                this.weekDays = weekDays;
                this.timeBookings = initiativeWithTicketsAndTimeBooking;
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
    },
    mounted() {
        this.getTimeBookingData();
    },
}
</script>

<style>
/* Add any custom styles here */
.list-group {
    margin-bottom: 0;
}

.list-group-item {
    border: 1px solid #dee2e6;
}

/* Adjust flex properties to ensure uniformity in appearance */
.list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    /* Allow wrapping for small screens */
}
</style>