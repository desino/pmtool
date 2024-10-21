<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('initiative_time_booking.page_title') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="row w-100 mb-3 align-items-center">
            <div class="col-md-2">
                <select v-model="filter.initiative_id" class="form-select"
                    @change="getProjectListAndInitiativeTimeBookings($event)">
                    <option value="">{{ $t('initiative_time_booking.list_filter.initiative') }}</option>
                    <option v-for="initiative in initiatives" :key="initiative.id" :value="initiative.id">
                        {{ initiative.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select v-model="filter.project_id" class="form-select" :disabled="disabledProjectList()"
                    @change="getInitiativeTimeBookings">
                    <option value="">{{ $t('initiative_time_booking.list_filter.project') }}</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">
                        {{ project.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <select v-model="filter.user_id" class="form-select" @change="getInitiativeTimeBookings">
                    <option value="">{{ $t('initiative_time_booking.list_filter.user') }}</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                        {{ user.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-2">
                <input v-model="filter.days" :placeholder="$t('initiative_time_booking.list_filter.days')"
                    class="form-control" type="text" @keyup="getInitiativeTimeBookings" @input="allowOnlyNumbers">
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="filter.include_mapped"
                        @change="getInitiativeTimeBookings" id="include_mapped">
                    <label class="form-check-label fw-bold" for="include_mapped">
                        {{ $t('initiative_time_booking.list_filter.include_mapped') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="row w-100 mb-3 align-items-center">
            <div class="col-md-2">
                <button class="btn btn-desino" type="button"
                    :disabled="!selectedTimeBookings.length > 0 || filter.initiative_id == ''"
                    @click="bulkAssignProjectForInitiativeTimeBookings">
                    {{ $t('initiative_time_booking.list.button.bulk_assign_project') }}
                </button>
            </div>
        </div>
        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="font-weight-bold bg-desino text-white rounded-top list-group-item">
                <div class="row w-100">
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        <input class="form-check-input mx-1" type="checkbox" v-model="isChkAllTimeBookings"
                            @change="handleSelectAllTimeBookings">
                        {{ $t('initiative_time_booking.list_table.date') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('initiative_time_booking.list_table.user') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('initiative_time_booking.list_table.time') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('initiative_time_booking.list_table.initiative_name') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('initiative_time_booking.list_table.project_name') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('initiative_time_booking.list_table.description') }}
                    </div>
                </div>
            </li>
            <li v-if="initiativeTimeBookingList.length > 0"
                v-for="(initiativeTimeBooking, index) in initiativeTimeBookingList" :key="index"
                class="border list-group-item">
                <div class="row w-100">
                    <div class="col-lg-2 col-md-6 col-6 align-items-center">
                        <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + initiativeTimeBooking.id"
                            v-model="initiativeTimeBooking.isChecked"
                            @change="handleSelectTimeBookings(initiativeTimeBooking)" style="margin-right: 10px;">
                        {{ initiativeTimeBooking.show_booked_date }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        {{ initiativeTimeBooking.user?.name }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 text-end">
                        {{ initiativeTimeBooking.hours }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        {{ initiativeTimeBooking.initiative?.name }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6">
                        {{ initiativeTimeBooking.project?.name }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6" v-html="initiativeTimeBooking.comments">
                    </div>
                </div>
            </li>
            <li v-else class="list-group-item border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('initiative_time_booking.list_table.no_data_text') }}
                </div>
            </li>
        </ul>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="getInitiativeTimeBookings" />
        <div id="initiativeTimeBookingForAssignProjectModal" aria-hidden="true"
            aria-labelledby="initiativeTimeBookingForAssignProjectLabel" class="modal fade" tabindex="-1">
            <InitiativeTimeBookingForAssignProjectModalComponent
                ref="initiativeTimeBookingForAssignProjectModalComponent" />
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import { Modal } from 'bootstrap';
import { mapActions, mapGetters } from 'vuex';
import GlobalMessage from '../../components/GlobalMessage.vue';
import messageService from '../../services/messageService';
import InitiativeTimeBookingService from '../../services/InitiativeTimeBookingService';
import PaginationComponent from '../../components/PaginationComponent.vue';
import InitiativeTimeBookingForAssignProjectModalComponent from './InitiativeTimeBookingForAssignProjectModalComponent.vue';
export default {
    name: 'InitiativeTimeBookingsComponent',
    components: {
        GlobalMessage,
        PaginationComponent,
        InitiativeTimeBookingForAssignProjectModalComponent
    },
    data() {
        return {
            initiatives: [],
            users: [],
            projects: [],
            selectedTimeBookings: [],
            isChkAllTimeBookings: false,
            initiativeTimeBookingList: [],
            currentPage: "",
            totalPages: "",
            filter: {
                initiative_id: "",
                project_id: "",
                user_id: "",
                days: "",
                include_mapped: false
            },
            error: {},
            showMessage: true
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async getInitialDataForInitiativeTimeBookings() {
            try {
                const { content: { initiatives, users } } = await InitiativeTimeBookingService.getInitialDataForInitiativeTimeBookings();
                this.initiatives = initiatives;
                this.users = users;
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitiativeTimeBookings(page = 1) {
            this.selectedTimeBookings = [];
            this.isChkAllTimeBookings = false;
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    page: page,
                    filter: this.filter
                }
                const { content: { data, current_page, last_page } } = await InitiativeTimeBookingService.getInitiativeTimeBookings(params);
                this.initiativeTimeBookingList = data;
                this.currentPage = current_page;
                this.totalPages = last_page;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async getProjectListAndInitiativeTimeBookings(event) {
            const initiativeId = event.target.value;
            this.clearMessages();
            try {
                const params = {
                    initiative_id: initiativeId,
                }
                const { content } = await InitiativeTimeBookingService.getProjectListForInitiativeTimeBookings(params);
                this.projects = content;
                this.getInitiativeTimeBookings();
            } catch (error) {
                this.handleError(error);
            }
        },
        bulkAssignProjectForInitiativeTimeBookings() {
            if (!this.selectedTimeBookings.length > 0) {
                return false;
            }
            const passData = {
                projects: this.projects,
                time_booking_ids: this.selectedTimeBookings,
                initiative_id: this.filter.initiative_id
            }
            this.$refs.initiativeTimeBookingForAssignProjectModalComponent.getInitialDataForInitiativeTimeBookingsAssignProject(passData);
            const modalElement = document.getElementById('initiativeTimeBookingForAssignProjectModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        handleSelectAllTimeBookings() {
            this.selectedTimeBookings = [];
            this.initiativeTimeBookingList = this.initiativeTimeBookingList.map(initiativeTimeBooking => {
                initiativeTimeBooking.isChecked = this.isChkAllTimeBookings;
                if (this.isChkAllTimeBookings) {
                    this.selectedTimeBookings.push(initiativeTimeBooking.id);
                }
                return initiativeTimeBooking;
            });
        },
        handleSelectTimeBookings(initiativeTimeBooking) {
            if (initiativeTimeBooking.isChecked) {
                if (!this.selectedTimeBookings.includes(initiativeTimeBooking.id)) {
                    this.selectedTimeBookings.push(initiativeTimeBooking.id);
                }
            } else {
                this.selectedTimeBookings = this.selectedTimeBookings.filter(id => id !== initiativeTimeBooking.id);
            }
            this.isChkAllTimeBookings = this.initiativeTimeBookingList.every(initiativeTimeBooking => initiativeTimeBooking.isChecked);
        },
        allowOnlyNumbers(event) {
            const value = event.target.value.replace(/\D/g, '');
            this.filter.days = value;
        },
        disabledProjectList() {
            if (!this.filter.initiative_id || !this.filter.include_mapped) {
                this.filter.project_id = '';
                return true;
            }
            return false;
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
        this.getInitiativeTimeBookings();
        this.clearMessages();
        this.getInitialDataForInitiativeTimeBookings();
    },
}
</script>