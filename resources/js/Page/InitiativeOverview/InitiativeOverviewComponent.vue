<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-xl-5 col-lg-4 col-md-6 col-6 ">
                            {{ $t('initiative_overview_list.name_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center">
                            {{ $t('initiative_overview_list.total_tickets_count_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center">
                            {{ $t('initiative_overview_list.visible_tickets_count_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center">
                            {{ $t('initiative_overview_list.invisible_tickets_count_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-2 d-none d-lg-block text-center">
                            {{ $t('initiative_overview_list.estimation_hours_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-2 d-none d-lg-block text-center">
                            {{ $t('initiative_overview_list.visible_estimation_hours_text') }}
                        </div>
                        <div class="col-xl-2 col-lg-1 d-none d-lg-block text-end">
                            {{ $t('initiative_overview_list.action_text') }}
                        </div>
                    </div>
                </li>
                <li v-if="initiatives.length > 0" v-for="(initiative, index) in initiatives" :key="index"
                    class="list-group-item p-1 list-group-item-action">
                    <div class="row g-1 align-items-center"  style="min-height: 48px;" role="button" >
                        <div class="col-xl-5 col-lg-4 col-md-6 col-6" @click="redirectInitiativeTicketsPage(initiative)">
                            {{ initiative.client_initiative_name }}
                        </div>
                        <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center" @click="redirectInitiativeTicketsPage(initiative)">
                            {{ initiative.total_ticket_count }}
                        </div>
                        <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center" @click="redirectInitiativeTicketsPage(initiative)">
                            {{ initiative.visible_ticket_count }}
                        </div>
                        <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center" @click="redirectInitiativeTicketsPage(initiative)">
                            {{ initiative.invisible_ticket_count }}
                        </div>
                        <div class="col-xl-1 offset-lg-0 col-lg-2 offset-md-6 col-md-2 offset-6 col-2 text-center" @click="redirectInitiativeTicketsPage(initiative)">
                            <span class="badge rounded-3 bg-success-subtle text-success rounded-top mt-2 small">
                                {{ initiative.total_ticket_estimation_hours ?? 0 }}
                                {{ $t('initiative_overview_list.hours_text') }}
                            </span>
                        </div>
                        <div class="col-xl-1 col-lg-2 col-md-2 col-2 text-center" @click="redirectInitiativeTicketsPage(initiative)">
                            <span class="badge rounded-3 bg-success-subtle text-success ">
                                {{ initiative.visible_ticket_estimation_hours ?? 0 }}
                                {{ $t('initiative_overview_list.hours_text') }}
                            </span>
                        </div>
                        <div class="col-xl-2 col-lg-1 col-md-2 col-2 text-end">
                            <div class="dropdown">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="editInitiativeOverview(initiative)">
                                            {{ $t('initiative_overview_list.edit_action_tooltip_text') }}
                                        </a>
                                    </li>
                                    <li class="small">
                                        <router-link role="button" class="btn btn-sm btn-desino w-100 small" target="_blank"
                                            :to="{ name: 'activity-logs', query: { initiative_id: initiative.id } }" >
                                            {{ $t('initiative_overview_list.logs_action_text') }}
                                        </router-link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('initiative_overview_list.no_initiative_overview_found_text') }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div id="editInitiativeOverviewModal" aria-hidden="true" aria-labelledby="editInitiativeOverviewModalLabel"
            class="modal fade" tabindex="-1">
            <EditOpportunityModalComponent ref="editInitiativeOverviewModalComponent"
                @pageUpdated="getInitiativeOverviewData" />
        </div>
    </div>
</template>

<script>
import store from '../../store';
import { mapActions, mapGetters } from 'vuex';
import showToast from '../../utils/toasts';
import messageService from '../../services/messageService';
import GlobalMessage from '../../components/GlobalMessage.vue';
import InitiativeOverviewService from '../../services/InitiativeOverviewService';
import { Modal, Tooltip } from 'bootstrap';
import EditOpportunityModalComponent from './../../Page/Opportunity/EditOpportunityModal.vue';
import OpportunityService from '../../services/OpportunityService';


export default {
    name: 'InitiativeOverviewComponent',
    components: {
        GlobalMessage,
        EditOpportunityModalComponent
    },
    data() {
        return {
            filter: {

            },
            initiatives: [],
            showMessage: true,
            errors: {},
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async getInitiativeOverviewData() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    filters: this.filter
                }
                const { content: { initiatives } } = await InitiativeOverviewService.getInitiativeOverviewData(params);
                this.initiatives = initiatives;
                await this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        redirectInitiativeTicketsPage(initiative) {
            const ticketDetailRoute = this.$router.resolve({ name: 'tasks', params: { id: initiative.id } });
            window.open(ticketDetailRoute.href, '_blank');
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        async editInitiativeOverview(initiative) {
            const response = await OpportunityService.getOpportunity(initiative.id);
            this.$refs.editInitiativeOverviewModalComponent.getEditOpportunityFormData(response.content);
            const modalElement = document.getElementById('editInitiativeOverviewModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
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
        this.getInitiativeOverviewData();
        const setHeaderData = {
            page_title: this.$t('initiative_overview.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
    },
}
</script>
