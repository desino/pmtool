<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-md-2">
                    <select v-model="filter.initiative_id" class="form-select"
                        @change="getProjectListAndTimeMappings($event)">
                        <option value="">{{ $t('time_mapping.list_filter.initiative') }}</option>
                        <option v-for="initiative in initiatives" :key="initiative.id" :value="initiative.id">
                            {{ initiative.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select v-model="filter.project_id" class="form-select" :disabled="disabledProjectList()"
                        @change="getTimeMappings">
                        <option value="">{{ $t('time_mapping.list_filter.project') }}</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">
                            {{ project.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select v-model="filter.user_id" class="form-select" @change="getTimeMappings">
                        <option value="">{{ $t('time_mapping.list_filter.user') }}</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input v-model="filter.days" :placeholder="$t('time_mapping.list_filter.days')" class="form-control"
                        type="text" @keyup="getTimeMappings" @input="allowOnlyNumbers">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="filter.include_mapped"
                            @change="getTimeMappings" id="include_mapped">
                        <label class="form-check-label fw-bold" for="include_mapped">
                            {{ $t('time_mapping.list_filter.include_mapped') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-md-2">
                    <button class="btn btn-desino w-100" type="button"
                        :disabled="!selectedTimeBookings.length > 0 || filter.initiative_id == ''"
                        @click="bulkAssignProjectForTimeMappings">
                        {{ $t('time_mapping.list.button.bulk_assign_project') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-2 col-md-6 col-6">
                            <input class="form-check-input mx-1" type="checkbox" v-model="isChkAllTimeBookings"
                                @change="handleSelectAllTimeBookings">
                            {{ $t('time_mapping.list_table.date') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('time_mapping.list_table.user') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('time_mapping.list_table.time') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('time_mapping.list_table.initiative_name') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('time_mapping.list_table.project_name') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('time_mapping.list_table.description') }}
                        </div>
                    </div>
                </li>
                <li v-if="timeMappingList.length > 0" v-for="(timeMapping, index) in timeMappingList" :key="index"
                    class="list-group-item p-1 list-group-item-action">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-lg-2 col-md-6 col-6">
                            <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + timeMapping.id"
                                v-model="timeMapping.isChecked" @change="handleSelectTimeBookings(timeMapping)"
                                style="margin-right: 10px;">
                                <span class="badge bg-secondary text-wrap">{{ timeMapping.show_booked_date }}</span>
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ timeMapping.user?.name }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                {{ timeMapping.hours }}{{ $t('ticket.list.estimation_hours_text') }}
                            </span>
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ timeMapping.initiative?.name }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ timeMapping.project?.name }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6" v-html="timeMapping.comments">
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('time_mapping.list_table.no_data_text') }}
                        </div>
                    </div>
                </li>
            </ul>
            <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
                @page-changed="getTimeMappings" />
        </div>
        <div id="timeMappingForAssignProjectModal" aria-hidden="true" aria-labelledby="timeMappingForAssignProjectLabel"
            class="modal fade" tabindex="-1">
            <TimeMappingForAssignProjectModalComponent ref="timeMappingForAssignProjectModalComponent"
                @pageUpdated="getTimeMappings" />
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import { Modal } from 'bootstrap';
import { mapActions, mapGetters } from 'vuex';
import GlobalMessage from '../../components/GlobalMessage.vue';
import messageService from '../../services/messageService';
import TimeMappingService from '../../services/TimeMappingService';
import PaginationComponent from '../../components/PaginationComponent.vue';
import store from '../../store';
import TimeMappingForAssignProjectModalComponent from './TimeMappingForAssignProjectModalComponent.vue';
export default {
    name: 'TimeMappingsComponent',
    components: {
        GlobalMessage,
        PaginationComponent,
        TimeMappingForAssignProjectModalComponent
    },
    data() {
        return {
            initiatives: [],
            users: [],
            projects: [],
            selectedTimeBookings: [],
            isChkAllTimeBookings: false,
            timeMappingList: [],
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
        async getInitialDataForTimeMappings() {
            try {
                const { content: { initiatives, users } } = await TimeMappingService.getInitialDataForTimeMappings();
                this.initiatives = initiatives;
                this.users = users;
            } catch (error) {
                this.handleError(error);
            }
        },
        async getTimeMappings(page = 1) {
            this.selectedTimeBookings = [];
            this.isChkAllTimeBookings = false;
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    page: page,
                    filter: this.filter
                }
                const { content: { data, current_page, last_page } } = await TimeMappingService.getTimeMappings(params);
                this.timeMappingList = data;
                this.currentPage = current_page;
                this.totalPages = last_page;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async getProjectListAndTimeMappings(event) {
            const initiativeId = event.target.value;
            this.clearMessages();
            try {
                const params = {
                    initiative_id: initiativeId,
                }
                const { content } = await TimeMappingService.getProjectListForTimeMappings(params);
                this.projects = content;
                this.getTimeMappings();
            } catch (error) {
                this.handleError(error);
            }
        },
        bulkAssignProjectForTimeMappings() {
            if (!this.selectedTimeBookings.length > 0) {
                return false;
            }
            const passData = {
                projects: this.projects,
                time_booking_ids: this.selectedTimeBookings,
                initiative_id: this.filter.initiative_id
            }
            this.$refs.timeMappingForAssignProjectModalComponent.getInitialDataForTimeMappingsAssignProject(passData);
            const modalElement = document.getElementById('timeMappingForAssignProjectModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        handleSelectAllTimeBookings() {
            this.selectedTimeBookings = [];
            this.timeMappingList = this.timeMappingList.map(timeMapping => {
                timeMapping.isChecked = this.isChkAllTimeBookings;
                if (this.isChkAllTimeBookings) {
                    this.selectedTimeBookings.push(timeMapping.id);
                }
                return timeMapping;
            });
        },
        handleSelectTimeBookings(timeMapping) {
            if (timeMapping.isChecked) {
                if (!this.selectedTimeBookings.includes(timeMapping.id)) {
                    this.selectedTimeBookings.push(timeMapping.id);
                }
            } else {
                this.selectedTimeBookings = this.selectedTimeBookings.filter(id => id !== timeMapping.id);
            }
            this.isChkAllTimeBookings = this.timeMappingList.every(timeMapping => timeMapping.isChecked);
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
        this.getTimeMappings();
        this.clearMessages();
        this.getInitialDataForTimeMappings();
        const setHeaderData = {
            page_title: this.$t('time_mapping.page_title')
        }
        store.commit("setHeaderData", setHeaderData);
    },
}
</script>
