<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('time_booking.page_title') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100">
            <div class="table-responsive">
                <table class="table table-bordered w-100">
                    <thead>
                        <tr class="bg-desino">
                            <th class="sticky-col sticky-col-1 bg-transparent text-center text-white align-middle p-1">
                                <select class="form-select form-select-sm" v-model="filter.initiative_id"
                                    @change="handleInitiativeFilterChange(true)">
                                    <option value="">{{ $t('time_booking.list_table.initiative_column') }}</option>
                                    <option v-for="initiative in initiativesFilterList" :key="initiative.id"
                                        :value="initiative.initiative_id">
                                        {{ initiative.initiative_name }}
                                    </option>
                                </select>

                                <!-- <multiselect v-model="filter.initiative_id" :options="initiativesFilterList"
                                    :placeholder="$t('create_ticket_modal_select_functionality_placeholder')"
                                    label="initiative_name" track-by="initiative_id"
                                    @select="handleInitiativeFilterChange" @remove="handleInitiativeFilterChange">
                                </multiselect> -->
                            </th>

                            <th class="sticky-col sticky-col-2 bg-transparent text-center text-white align-middle p-1">
                                <select class="form-select form-select-sm" v-model="filter.ticket_id"
                                    @change="handleTicketFilterChange">
                                    <option value="">{{ $t('time_booking.list_table.ticket_column') }}</option>
                                    <option v-for="ticket in selectBoxTicketsFilterList" :key="ticket.id"
                                        :value="ticket.ticket_id">
                                        {{ ticket.ticket_name }}
                                    </option>
                                </select>
                            </th>

                            <th class="bg-dark text-center align-middle p-1" width="40px;">
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

                            <th class="bg-dark text-center align-middle p-1" width="40px;">
                                <a class="text-white" href="javascript:void(0);" @click="getTimeBookingData(1)">
                                    <i class="bi bi-caret-right"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th class="sticky-col sticky-col-1 bg-opacity-25 bg-primary text-center align-middle p-1"
                                colspan="2">
                                {{ $t('time_booking.list_table.total_hours') }}
                            </th>
                            <td :rowspan="thRowSpanCount"></td>
                            <td class="text-center align-middle p-1" v-for="(weekDay, index) in weekDays" :key="index">
                                <small v-if="weekDay.total_hours > 0" class="badge text-white bg-secondary"
                                    style="font-size: 0.8rem;">
                                    {{ weekDay.total_hours }}
                                </small>
                            </td>
                            <td :rowspan="thRowSpanCount"></td>
                        </tr>

                        <template v-for="(timeBooking, timeBookingIndex) in timeBookings" :key="timeBookingIndex">
                            <tr v-if="timeBooking.initiative_id">
                                <th class="sticky-col sticky-col-1 text-left p-1"
                                    :rowspan="timeBooking.tickets.length + 1">
                                    <small>{{ timeBooking.initiative_name }}</small>
                                </th>
                            </tr>

                            <tr v-if="!timeBooking.initiative_id">
                                <th class="sticky-col sticky-col-1 text-left p-1 bg-opacity-25 bg-warning text-center"
                                    :colspan="2" :rowspan="timeBooking.tickets.length + 1">
                                    <small>{{ timeBooking.initiative_name }}</small>
                                </th>
                            </tr>

                            <tr v-for="(ticket, ticketIndex) in timeBooking.tickets" :key="ticketIndex">
                                <th v-if="timeBooking.initiative_id"
                                    class="sticky-col sticky-col-2 text-left align-middle p-1" :class="{
                                        'bg-info text-white': ticketIndex == 0,
                                        'bg-warning': timeBooking.tickets.length - 1 == ticketIndex
                                    }">
                                    <small>{{ ticket.ticket_name }}</small>
                                </th>

                                <td class="text-center align-middle p-1"
                                    :role="ticket.hours_per_day[weekDay.date]?.is_allow_booking ? 'button' : false"
                                    v-for="(weekDay, index) in weekDays" :key="index"
                                    @click="openTimeBookingModal(timeBooking, weekDay, ticket.hours_per_day[weekDay.date]?.is_allow_booking, ticketIndex, ticket)">
                                    <span v-if="timeBooking.initiative_id" class="badge text-secondary">
                                        {{ ticket.hours_per_day[weekDay.date]?.hours > 0 ?
                                            ticket.hours_per_day[weekDay.date]?.hours : '' }}
                                    </span>
                                    <span v-if="!timeBooking.initiative_id" class="badge text-secondary"
                                        v-html="ticket.hours_per_day[weekDay.date]?.hours"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="timeBookingModal" aria-hidden="true" aria-labelledby="timeBookingModalLabel" class="modal fade"
            tabindex="-1">
            <TimeBookingModalComponent ref="timeBookingModalComponent" @pageUpdated="getTimeBookingEmitData" />
        </div>
        <div id="timeBookingOnNewTicketModal" aria-hidden="true" aria-labelledby="timeBookingOnNewTicketModalLabel"
            class="modal fade" tabindex="-1">
            <TimeBookingOnNewTicketModalComponent ref="timeBookingOnNewTicketModalComponent"
                @pageUpdated="getTimeBookingEmitData" />
        </div>
        <div id="timeBookingOnNewInitiativeOrTicketModal" aria-hidden="true"
            aria-labelledby="timeBookingOnNewInitiativeOrTicketModalLabel" class="modal fade" tabindex="-1">
            <TimeBookingOnNewInitiativeOrTicketModalComponent ref="timeBookingOnNewInitiativeOrTicketModalComponent"
                @pageUpdated="getTimeBookingEmitData" />
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
import { Modal } from 'bootstrap';
import TimeBookingModalComponent from './TimeBookingModalComponent.vue';
import TimeBookingOnNewTicketModalComponent from './TimeBookingOnNewTicketModalComponent.vue';
import TimeBookingOnNewInitiativeOrTicketModalComponent from './TimeBookingOnNewInitiativeOrTicketModalComponent.vue';
import { handleError, nextTick } from 'vue';
import Multiselect from 'vue-multiselect';
export default {
    name: 'BookingComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        TimeBookingModalComponent,
        TimeBookingOnNewTicketModalComponent,
        TimeBookingOnNewInitiativeOrTicketModalComponent,
        Multiselect
    },
    data() {
        return {
            timeBookings: [],
            forFilterTimeBooking: [],
            weekDays: [],
            thRowSpanCount: 0,
            filter: {
                initiative_id: "",
                ticket_id: ""
            },
            initiativesFilterList: [],
            ticketsFilterList: [],
            selectBoxTicketsFilterList: [],
            errors: {},
            showMessage: true,
        }
    },
    // watch: {
    //     filter: {
    //         deep: true,
    //         handler(newValue) {
    //             this.handleInitiativeFilterChange();
    //         }
    //     }
    // },
    methods: {
        ...mapActions(['setLoading']),
        async getTimeBookingData(number = 0) {
            this.clearMessages();
            this.setLoading(true);
            try {
                const passData = {
                    previous_or_next_of_week: number,
                    start_date: this.weekDays[0]?.date,
                    end_date: this.weekDays[this.weekDays.length - 1]?.date,
                }
                const { content: { weekDays, initiativeWithTicketsAndTimeBooking, thRowSpanCount } } = await TimeBookingService.getTimeBookingData(passData);
                // this.thRowSpanCount = thRowSpanCount;
                this.weekDays = weekDays;
                this.timeBookings = initiativeWithTicketsAndTimeBooking;
                this.forFilterTimeBooking = initiativeWithTicketsAndTimeBooking;
                this.getInitiativesAndTicketsFilterList();
                this.setLoading(false);
                this.handleInitiativeFilterChange();
                this.handleTicketFilterChange();
            } catch (error) {
                this.handleError(error);
            }
        },
        openTimeBookingModal(timeBooking, weekDay, isAllowBooking, ticketIndex, ticket = {}) {
            if (!isAllowBooking) {
                return;
            }
            if (!timeBooking.initiative_id) {
                const timeBookingOnNewInitiativeOrTicketModalElement = document.getElementById('timeBookingOnNewInitiativeOrTicketModal');
                if (timeBookingOnNewInitiativeOrTicketModalElement) {
                    this.$refs.timeBookingOnNewInitiativeOrTicketModalComponent.getTimeBookingData(weekDay, this.weekDays);
                    const timeBookingOnNewInitiativeOrTicketModal = new Modal(timeBookingOnNewInitiativeOrTicketModalElement);
                    timeBookingOnNewInitiativeOrTicketModal.show();
                }
            } else if (timeBooking.initiative_id && ticketIndex > 0 && !ticket.ticket_id) {
                const timeBookingOnNewTicketModalElement = document.getElementById('timeBookingOnNewTicketModal');
                if (timeBookingOnNewTicketModalElement) {
                    this.$refs.timeBookingOnNewTicketModalComponent.getTimeBookingData(timeBooking, weekDay, this.weekDays);
                    const timeBookingOnNewTicketModal = new Modal(timeBookingOnNewTicketModalElement);
                    timeBookingOnNewTicketModal.show();
                }
            } else {
                const modalElement = document.getElementById('timeBookingModal');
                if (modalElement) {
                    this.$refs.timeBookingModalComponent.getTimeBookingData(timeBooking, weekDay, this.weekDays, ticket);
                    const modal = new Modal(modalElement);
                    modal.show();
                }
            }
        },
        getTimeBookingEmitData(weekDays) {
            this.weekDays = weekDays;
            this.getTimeBookingData();
        },
        getInitiativesAndTicketsFilterList() {
            this.initiativesFilterList = [];
            this.ticketsFilterList = [];
            this.forFilterTimeBooking.forEach((timeBooking, timeBookingIndex) => {
                if (!this.initiativesFilterList.includes(timeBooking.initiative_id) && timeBooking.initiative_id) {
                    const initiativeData = {
                        initiative_id: timeBooking.initiative_id,
                        initiative_name: timeBooking.initiative_name
                    };
                    this.initiativesFilterList.push(initiativeData);
                }
                timeBooking.tickets.forEach((ticket, ticketIndex) => {
                    if ((!this.ticketsFilterList.includes(ticket.ticket_id) && ticket.ticket_id && ticket.ticket_id != '')) {
                        let ticketId = ticket.ticket_id;
                        let initiativeId = timeBooking.initiative_id;
                        // if (timeBookingIndex == 0 && ticketIndex == 0) {
                        //     ticketId = 0;
                        //     initiativeId = '';
                        // }
                        const ticketData = {
                            initiative_id: initiativeId,
                            ticket_id: ticketId,
                            ticket_name: ticket.ticket_name
                        };
                        this.ticketsFilterList.push(ticketData);
                    }
                })
            });
            if (this.initiativesFilterList.length == 0) {
                this.ticketsFilterList = [];
            }
            this.selectBoxTicketsFilterList = this.ticketsFilterList;
        },
        handleInitiativeFilterChange(ifOnchange = false) {
            // const filterInitiativeId = this.filter.initiative_id?.initiative_id;
            const filterInitiativeId = this.filter.initiative_id;
            this.filter.ticket_id = this.filter.ticket_id ?? '';
            if (ifOnchange) {
                this.filter.ticket_id = "";
            }
            if (filterInitiativeId == undefined || filterInitiativeId == '') {
                this.timeBookings = this.forFilterTimeBooking;
                this.selectBoxTicketsFilterList = this.ticketsFilterList;
            } else {
                setTimeout(() => {
                    this.timeBookings = this.forFilterTimeBooking.filter(timeBooking => timeBooking.initiative_id === filterInitiativeId);
                    this.selectBoxTicketsFilterList = this.ticketsFilterList.filter(ticket => ticket.initiative_id === filterInitiativeId || ticket.initiative_id === '');
                    this.calculateWeekDaysHours();
                })
            }
        },
        handleTicketFilterChange() {
            const filterTicketId = this.filter.ticket_id;
            if (filterTicketId == '') {
                this.timeBookings = this.forFilterTimeBooking;
            } else {
                const selectedTicket = this.ticketsFilterList.find(ticket => ticket.ticket_id === filterTicketId);
                const initiativeBaseOnTicket = this.forFilterTimeBooking.filter(timeBooking => timeBooking.initiative_id === selectedTicket.initiative_id);

                const initiativeTickets = initiativeBaseOnTicket.map(initiative => {
                    return {
                        ...initiative,
                        tickets: initiative.tickets.filter(ticket => ticket.ticket_id === filterTicketId) // Filter products by 'inStock'
                    };
                });
                setTimeout(() => {
                    this.timeBookings = initiativeTickets;
                    this.calculateWeekDaysHours();
                })
            }
            if (this.filter.initiative_id) {
                this.handleInitiativeFilterChange();
            }
        },
        calculateWeekDaysHours() {
            const ticketsHoursPerDay = this.timeBookings.flatMap(timeBooking => timeBooking.tickets).flatMap(ticket => ticket.hours_per_day);
            const sumPerDate = {};
            ticketsHoursPerDay.forEach((currentTicketDates) => {
                Object.keys(currentTicketDates).forEach(date => {
                    const hours = parseFloat(currentTicketDates[date].hours) || 0;
                    sumPerDate[date] = (sumPerDate[date] || 0) + hours;
                });
            });
            this.weekDays.forEach(weekDay => {
                weekDay.total_hours = sumPerDate[weekDay.date] || 0;
            });
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
        this.clearMessages();
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