<template>
    <GlobalMessage v-if="showMessage" />
    <div class="w-100 mb-3 g-1">
        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                <div class="row g-1 w-100 align-items-center">
                    <div class="col-xl-5 col-lg-4 col-md-6 col-6 fw-bold small ">
                        {{ $t('initiative_overview_list.name_text') }}
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2 col-2 fw-bold small text-center">
                        {{ $t('initiative_overview_list.total_tickets_count_text') }}
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2 col-2 fw-bold small text-center">
                        {{ $t('initiative_overview_list.visible_tickets_count_text') }}
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2 col-2 fw-bold small text-center">
                        {{ $t('initiative_overview_list.invisible_tickets_count_text') }}
                    </div>
                    <div class="col-xl-1 col-lg-2 d-none d-lg-block fw-bold small text-center">
                        {{ $t('initiative_overview_list.estimation_hours_text') }}
                    </div>
                    <div class="col-xl-1 col-lg-2 d-none d-lg-block fw-bold small text-center">
                        {{ $t('initiative_overview_list.visible_estimation_hours_text') }}
                    </div>
                    <div class="col-xl-2 col-lg-1 d-none d-lg-block fw-bold small text-end">
                        {{ $t('initiative_overview_list.action_text') }}
                    </div>
                </div>
            </li>
            <li v-if="initiatives.length > 0" v-for="(initiative, index) in initiatives" :key="index"
                class="border list-group-item p-1 list-group-item-action border-top-0">
                <div class="row g-1 w-100 align-items-center" role="button"
                    @click="redirectInitiativeTicketsPage(initiative)">
                    <div class="col-xl-5 col-lg-4 col-md-6 col-6 ">
                        {{ initiative.client_initiative_name }}
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center">
                        {{ initiative.total_ticket_count }}
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center">
                        {{ initiative.visible_ticket_count }}
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2 col-2 text-center">
                        {{ initiative.invisible_ticket_count }}
                    </div>
                    <div class="col-xl-1 offset-lg-0 col-lg-2 offset-md-6 col-md-2 offset-6 col-2 text-center">
                        <span class="badge rounded-3 bg-success-subtle text-success rounded-top mt-2 small">
                            {{ initiative.total_ticket_estimation_hours ?? 0 }}
                            {{ $t('initiative_overview_list.hours_text') }}
                        </span>
                    </div>
                    <div class="col-xl-1 col-lg-2 col-md-2 col-2 text-center">
                        <span class="badge rounded-3 bg-success-subtle text-success ">
                            {{ initiative.visible_ticket_estimation_hours ?? 0 }}
                            {{ $t('initiative_overview_list.hours_text') }}
                        </span>
                    </div>
                    <div class="col-xl-2 col-lg-1 col-md-2 col-2 text-end">
                        <a :title="$t('initiative_overview_list.edit_action_tooltip_text')" data-bs-placement="bottom"
                            data-bs-toggle="tooltip" class="text-desino me-1" href="javascript:"
                            @click.stop="editInitiativeOverview(initiative)">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <router-link @click.stop
                            :to="{ name: 'activity-logs', query: { initiative_id: initiative.id } }"
                            class="text-decoration-none text-info" data-bs-placement="bottom" data-bs-toggle="tooltip"
                            :title="$t('initiative_overview_list.logs_action_text')" target="_blank">
                            <i class="bi bi-activity"></i>
                        </router-link>
                    </div>
                </div>
            </li>
            <li v-else class="border border-top-0 list-group-item px-0 py-1 list-group-item-action">
                <div class="fw-bold fst-italic text-center w-100">
                    {{ $t('initiative_overview_list.no_initiative_overview_found_text') }}
                </div>
            </li>
        </ul>
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