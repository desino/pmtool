<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <input v-model="filter.ticket_name" :placeholder="$t('activity_logs.filter.ticket_name')"
                            class="form-control" type="text" @keyup="getActivityLogsData">
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <multiselect v-model="filter.initiative_id" :multiple="false" :options="initiatives"
                            :searchable="true" select-label="" deselect-label="" label="client_initiative_name"
                            :placeholder="$t('activity_logs.filter.initiative_placeholder')" track-by="id"
                            @select="getActivityLogsData" @Remove="getActivityLogsData">
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <multiselect v-model="filter.activity_type" :multiple="true" :options="allActivityTypes"
                            :searchable="true" select-label="" deselect-label="" label="name"
                            :placeholder="$t('activity_logs.filter.activity_type_placeholder')" track-by="id"
                            @select="getActivityLogsData" @Remove="getActivityLogsData">
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <multiselect v-model="filter.activity_detail" :multiple="true" :options="allActivityDetails"
                            :searchable="true" select-label="" deselect-label="" label="name"
                            :placeholder="$t('activity_logs.filter.activity_details_placeholder')" track-by="id"
                            @select="getActivityLogsData" @Remove="getActivityLogsData">
                            <template #tag="{ option, remove }">
                                <span class="multiselect__tag_for_macro_status" :class="option.color">
                                    <span>{{ option.name }}</span>
                                    <i tabindex="1" class="multiselect__tag-icon" @click="remove(option)"></i>
                                </span>
                            </template>
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-3">
                    <div class="w-100 p-1">
                        <multiselect v-model="filter.user_id" :multiple="false" :options="users" :searchable="true"
                            select-label="" deselect-label="" label="name"
                            :placeholder="$t('activity_logs.filter.users_placeholder')" track-by="id"
                            @select="getActivityLogsData" @Remove="getActivityLogsData">
                        </multiselect>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush mb-3 mt-2">
                <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-4 col-md-6 col-8 fw-bold small ">
                            {{ $t('activity_logs_list.name_text') }}
                        </div>
                        <div class="col-lg-2 col-md-3 d-none d-md-block fw-bold small ">
                            {{ $t('activity_logs_list.activity_type_text') }}
                        </div>
                        <div class="col-lg-2 col-md-3 d-none d-md-block fw-bold small ">
                            {{ $t('activity_logs_list.activity_detail_text') }}
                        </div>
                        <div class="col-lg-2 col-md-2 d-none d-lg-block fw-bold small text-end text-lg-start">
                            {{ $t('activity_logs_list.user_text') }}
                        </div>
                        <div class="col-lg-2 d-none d-lg-block fw-bold small text-end">
                            {{ $t('activity_logs_list.date_time_text') }}
                        </div>
                    </div>
                </li>
                <li v-if="activityLogs.length > 0" v-for="(activityLog, index) in activityLogs" :key="index"
                    class="list-group-item p-1 list-group-item-action"
                    :role="isShowRoleButton(activityLog)" @click="redirectTicketDetailPage(activityLog)">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 col-md-6 col-lg-4" data-bs-toggle="tooltip" data-bs-placement="bottom"
                            :title="getTooltipTitle(activityLog)">
                            <small class="badge bg-secondary">{{ activityLog?.ticket?.initiative?.name ??
                                activityLog?.ticket_initiative_name }}</small>
                            {{ activityLog?.ticket?.composed_name ?? activityLog?.ticket_composed_name }}
                        </div>
                        <div class="offset-1 col-5 offset-md-0 col-md-3 col-lg-2">
                            {{ activityLog?.display_activity_type?.name }}
                        </div>
                        <div class="col-6 col-md-3 col-lg-2 text-center">
                            <span class="badge p-2 mt-1 w-100 text-wrap"
                                :class="activityLog?.display_activity_detail?.color">
                                {{ activityLog?.display_activity_detail?.name }}
                            </span>
                        </div>
                        <div class="offset-1 col-5 offset-md-6 col-md-3 offset-lg-0 col-lg-2">
                            {{ activityLog?.created_by?.name }}
                        </div>
                        <div class="col-6 col-md-3 col-lg-2 text-end">
                            <span class="badge bg-secondary text-wrap">
                                {{ activityLog?.display_created_at }}
                            </span>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('activity_logs_list.no_activity_logs_found_text') }}
                        </div>
                    </div>
                </li>
            </ul>
            <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
                @page-changed="getActivityLogsData" />
        </div>
    </div>
</template>

<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import PaginationComponent from '../../components/PaginationComponent.vue';
import ActivityLogsService from '../../services/ActivityLogsService';
import messageService from '../../services/messageService';
import store from '../../store';
import { mapActions, mapGetters } from 'vuex';
import Multiselect from "vue-multiselect";
import { Tooltip } from 'bootstrap';

export default {
    name: 'ActivityLogsComponent',
    components: {
        GlobalMessage,
        PaginationComponent,
        Multiselect
    },
    data() {
        return {
            filter: {
                ticket_name: "",
                initiative_id: "",
                activity_type: "",
                activity_detail: "",
                user_id: "",
            },
            activityLogs: [],
            currentPage: "",
            totalPages: "",
            initiatives: [],
            allActivityTypes: [],
            allActivityDetails: [],
            users: [],
            showMessage: true,
            errors: {},
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            try {
                this.clearMessages();
                await Promise.all([
                    this.getInitiativeDataForActivityLogs(),
                ]);
                this.setInitiativeIdForFilter();
                this.getActivityLogsData();
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitiativeDataForActivityLogs() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const { content: { initiatives, allActivityTypes, allActivityDetails, users } } = await ActivityLogsService.getInitiativeDataForActivityLogs();
                this.initiatives = initiatives;
                this.allActivityTypes = allActivityTypes;
                this.allActivityDetails = allActivityDetails;
                this.users = users;
                await this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        async getActivityLogsData(page = 1) {
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    page: page,
                    filters: this.filter
                }
                const { content: { data, current_page, last_page } } = await ActivityLogsService.getActivityLogsData(params);
                this.activityLogs = data;
                this.currentPage = current_page;
                this.totalPages = last_page;
                await this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        resetFilter() {
            this.filter.initiative_id = "";
            this.filter.activity_type = "";
            this.filter.activity_detail = "";
            this.filter.user_id = "";
        },
        setInitiativeIdForFilter() {
            this.resetFilter();
            let initiativeId = "";
            if ('initiative_id' in this.$route.query) {
                initiativeId = this.$route.query.initiative_id;
                this.filter.initiative_id = this.initiatives.find(initiative => initiative.id == initiativeId);
            }
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        getTooltipTitle(activityLog) {
            if (activityLog?.ticket?.initiative?.name == undefined && activityLog?.ticket?.ticket_composed_name == undefined) {
                return this.$t('activity_logs.tooltip_title_delete_ticket');
            }
            return;
        },
        isShowRoleButton(activityLog) {
            if (activityLog?.ticket?.initiative?.name == undefined && activityLog?.ticket?.ticket_composed_name == undefined) {
                return;
            }
            return 'button';
        },
        redirectTicketDetailPage(activityLog) {
            const ticketDetailRoute = this.$router.resolve({ name: 'task.detail', params: { initiative_id: activityLog?.ticket?.initiative?.id, ticket_id: activityLog?.ticket?.id } });
            window.open(ticketDetailRoute.href, '_blank');
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
        this.fetchData();
        const setHeaderData = {
            page_title: this.$t('activity_logs.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
    },
}
</script>
