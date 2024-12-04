<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content my-3">
        <div class="row g-0 w-100 align-items-center mb-3">
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
        <div class="w-100 mb-3 g-1">
            <ul class="list-group list-group-flush mb-3 mt-2">
                <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                    <div class="row g-1 w-100 align-items-center">
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
                    class="border list-group-item p-1 list-group-item-action border-top-0">
                    <div class="row g-1 w-100 align-items-center">
                        <div class="col-12 col-md-6 col-lg-4">
                            <small class="badge bg-secondary">{{ activityLog?.ticket?.initiative?.name }}</small>
                            {{ activityLog?.ticket?.composed_name }}
                            <!-- {{ activityLog?.ticket?.initiative?.client_initiative_name }} -->
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
                <li v-else class="border border-top-0 list-group-item px-0 py-1 list-group-item-action">
                    <div class="h4 fw-bold text-center">
                        {{ $t('activity_logs_list.no_activity_logs_found_text') }}
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
        // this.getInitiativeDataForActivityLogs();
        // setTimeout(() => {
        //     this.setInitiativeIdForFilter();
        //     this.getActivityLogsData();
        // }, 1000);
        this.fetchData();
        // this.$nextTick(() => {
        //     console.log('sdfdfsf :: ',);
        // });
        const setHeaderData = {
            page_title: this.$t('activity_logs.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
    },
}
</script>