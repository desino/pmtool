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
    <div class="app-content" id="timeBookingPageSection">
        <div class="w-100">
            <div class="scrolling outer">
                <div class="inner">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr class="header_row bg-transparent">
                            <th scope="col" class="border abs1 bg-transparent text-left text-white align-middle p-1"
                                style="height: 45px;">
                                <multiselect v-model="filter.initiative_id" :options="initiativesFilterList"
                                    :placeholder="$t('create_ticket_modal_select_functionality_placeholder')"
                                    label="initiative_name" track-by="initiative_id"
                                    @select="handleInitiativeFilterChange(true)"
                                    @remove="handleInitiativeFilterChange(true)">
                                </multiselect>
                            </th>
                            <th scope="col" class="border abs2 bg-transparent text-left text-white align-middle p-1"
                                style="height: 45px;">
                                <!-- <select class="form-select form-select-sm" v-model="filter.ticket_id"
                                    @change="handleTicketFilterChange">
                                    <option value="">{{ $t('time_booking.list_table.ticket_column') }}</option>
                                    <option v-for="ticket in selectBoxTicketsFilterList" :key="ticket.id"
                                        :value="ticket.ticket_id">
                                        {{ ticket.ticket_name }}
                                    </option>
                                </select> -->
                                <multiselect v-model="filter.ticket_id" :options="selectBoxTicketsFilterList"
                                    :placeholder="$t('create_ticket_modal_select_functionality_placeholder')"
                                    label="ticket_name" track-by="ticket_id" @select="handleTicketFilterChange"
                                    @remove="handleTicketFilterChange">
                                </multiselect>
                            </th>
                            <th scope="col" class="border abs3 bg-dark text-center align-middle p-1"
                                style="height: 45px;">
                                <a class="text-white" href="javascript:void(0);" @click="getTimeBookingData(-1)">
                                    <i class="bi bi-caret-left"></i>
                                </a>
                            </th>
                            <td scope="col" class="border text-center align-middle p-1"
                                :class="weekDay.is_today ? 'bg-black' : 'bg-desino'"
                                v-for="(weekDay, index) in weekDays" :key="index" style="height: 45px;">
                                <small class="small text-white" style="font-size: 0.8rem;">
                                    {{ weekDay.format_date }}
                                </small>
                            </td>
                            <th scope="col" class="border abs4 bg-dark text-center align-middle p-1"
                                style="height: 45px;">
                                <a class="text-white" href="javascript:void(0);" @click="getTimeBookingData(1)">
                                    <i class="bi bi-caret-right"></i>
                                </a>
                            </th>
                        </tr>
                        <tr>
                            <th class="border total_abs1 bg-opacity-25 bg-primary text-center align-middle p-1"
                                colspan="2">
                                {{ $t('time_booking.list_table.total_hours') }}
                            </th>
                            <th :rowspan="ticketRowsCount" class="border total_abs2 border">&nbsp;</th>
                            <td class="text-center align-middle p-1 border" v-for="(weekDay, index) in weekDays"
                                :key="index">
                                <small v-if="weekDay.total_hours > 0" class="badge text-white bg-secondary"
                                    style="font-size: 0.8rem;">
                                    {{ weekDay.total_hours > 0 ? weekDay.total_hours : '' }}
                                </small>
                                <small v-if="weekDay.total_hours == 0">&nbsp;</small>
                            </td>
                            <th :rowspan="ticketRowsCount" class="border total_abs3">&nbsp;</th>
                        </tr>

                        <tr class="bg-danger bg-opacity-25" v-if="unBillableRowData">
                            <th class="border total_abs1 bg-opacity-25 bg-danger text-center align-middle p-1"
                                colspan="2">
                                {{ unBillableRowData.initiative_name }}
                            </th>
                            <th :rowspan="ticketRowsCount" class="border total_abs2 border">&nbsp;</th>
                            <td class="text-center align-middle p-1 border" v-for="(weekDay, index) in weekDays"
                                :key="index"
                                :role="unBillableRowData.hours_per_day[weekDay.date]?.is_allow_booking ? 'button' : false"
                                @click="openUnBillableTimeBookingModal(unBillableRowData, weekDay, unBillableRowData.hours_per_day[weekDay.date]?.is_allow_booking)">
                                <span v-if="unBillableRowData.hours_per_day" class="badge text-secondary">
                                    {{ unBillableRowData.hours_per_day[weekDay.date]?.hours > 0 ?
                                        unBillableRowData.hours_per_day[weekDay.date]?.hours : ' ' }}
                                </span>
                            </td>
                            <th :rowspan="ticketRowsCount" class="border total_abs3">&nbsp;</th>
                        </tr>

                        <template v-for="(timeBooking, timeBookingIndex) in timeBookings" :key="timeBookingIndex">
                            <tr v-for="(ticket, ticketIndex) in timeBooking.tickets" :key="ticketIndex">
                                <th v-if="timeBooking.initiative_id && ticketIndex == 0"
                                    class="border abs1 text-left p-1" :rowspan="timeBooking.tickets.length">
                                    <small>{{ timeBooking.initiative_name }}</small>
                                </th>
                                <th v-if="timeBooking.initiative_id" class="border abs2 text-left align-middle p-1"
                                    :class="{
                                        'bg-info text-white': ticketIndex == 0,
                                        'bg-warning': timeBooking.tickets.length - 1 == ticketIndex
                                    }">
                                    <small>{{ ticket.ticket_name }}</small>
                                </th>
                                <th v-if="!timeBooking.initiative_id"
                                    class="border lastrow_abs text-left p-1 bg-opacity-25 bg-warning text-center"
                                    :colspan="2">
                                    <small>{{ timeBooking.initiative_name }}</small>
                                </th>
                                <td class="border text-center align-middle p-1 border"
                                    :role="ticket.hours_per_day[weekDay.date]?.is_allow_booking ? 'button' : false"
                                    v-for="(weekDay, index) in weekDays" :key="index"
                                    @click="openTimeBookingModal(timeBooking, weekDay, ticket.hours_per_day[weekDay.date]?.is_allow_booking, ticketIndex, ticket)">
                                    <span v-if="timeBooking.initiative_id" class="badge text-secondary">
                                        {{ ticket.hours_per_day[weekDay.date]?.hours > 0 ?
                                            ticket.hours_per_day[weekDay.date]?.hours : ' ' }}
                                    </span>
                                    <span v-if="timeBooking.initiative_id && ticket.ticket_id == '' && ticketIndex != 0"
                                        class="badge text-secondary" v-html="ticket.hours_per_day[weekDay.date]?.hours">
                                    </span>
                                    <span v-if="!timeBooking.initiative_id" class="badge text-secondary"
                                        v-html="ticket.hours_per_day[weekDay.date]?.hours">
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </table>
                </div>
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
        <div id="timeBookingUnBillableModal" aria-hidden="true" aria-labelledby="timeBookingUnBillableModalLabel"
            class="modal fade" tabindex="-1">
            <TimeBookingUnBillableModalComponent ref="timeBookingUnBillableModalComponent"
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
import TimeBookingUnBillableModalComponent from './TimeBookingUnBillableModalComponent.vue';
export default {
    name: 'BookingComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        TimeBookingModalComponent,
        TimeBookingOnNewTicketModalComponent,
        TimeBookingOnNewInitiativeOrTicketModalComponent,
        TimeBookingUnBillableModalComponent,
        Multiselect
    },
    data() {
        return {
            timeBookings: [],
            forFilterTimeBooking: [],
            weekDays: [],
            ticketRowsCount: 0,
            filter: {
                initiative_id: "",
                ticket_id: ""
            },
            initiativesFilterList: [],
            ticketsFilterList: [],
            selectBoxTicketsFilterList: [],
            unBillableRowData: [],
            unBillableProjects: [],
            errors: {},
            showMessage: true,
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
                    end_date: this.weekDays[this.weekDays.length - 1]?.date,
                }
                const { content: { weekDays, initiativeWithTicketsAndTimeBooking, ticketRowsCount, unBillableRowData, unBillableProjects } } = await TimeBookingService.getTimeBookingData(passData);
                this.ticketRowsCount = ticketRowsCount;
                this.weekDays = weekDays;
                this.timeBookings = initiativeWithTicketsAndTimeBooking;
                this.forFilterTimeBooking = initiativeWithTicketsAndTimeBooking;
                this.unBillableRowData = unBillableRowData;
                this.unBillableProjects = unBillableProjects;
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
            const filterInitiativeId = this.filter.initiative_id?.initiative_id;
            // const filterInitiativeId = this.filter.initiative_id;
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
                })
            }
            setTimeout(() => {
                this.calculateWeekDaysHours();
            })
        },
        handleTicketFilterChange() {
            const filterTicketId = this.filter.ticket_id?.ticket_id;
            if (filterTicketId == undefined || filterTicketId == '') {
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
            setTimeout(() => {
                this.calculateWeekDaysHours();
            })
            if (this.filter.initiative_id?.initiative_id && (filterTicketId == undefined || filterTicketId == '')) {
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
                weekDay.total_hours = sumPerDate[weekDay.date] + parseFloat(this.unBillableRowData?.hours_per_day[weekDay.date]?.hours) || 0;
            });
        },
        openUnBillableTimeBookingModal(timeBooking, weekDay, isAllowBooking) {
            if (!isAllowBooking) {
                return;
            }

            const timeBookingUnBillableModalElement = document.getElementById('timeBookingUnBillableModal');
            if (timeBookingUnBillableModalElement) {
                this.$refs.timeBookingUnBillableModalComponent.getTimeBookingData(timeBooking, weekDay, this.weekDays, this.unBillableProjects);
                const timeBookingUnBillableModal = new Modal(timeBookingUnBillableModalElement);
                timeBookingUnBillableModal.show();
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
        this.clearMessages();
        this.getTimeBookingData();
    },
}
</script>
