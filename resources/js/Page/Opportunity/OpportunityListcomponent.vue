<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="w-100 p-1">
                        <input v-model="filter.initiative_name"
                            :placeholder="$t('opportunity_list_table.search_placeholder_initiative_name')"
                            class="form-control" type="text" @keyup="fetchAllOpportunities">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="w-100 p-1">
                        <select id="client_id" v-model="filter.client_id" class="form-select"
                            @change="fetchAllOpportunities">
                            <option value="">{{ $t('opportunity_list_table.search_placeholder_client_id') }}</option>
                            <option v-for="client in filterClients" :key="client.id" :value="client.id">{{ client.name
                                }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="w-100 p-1">
                        <div class="form-check ms-auto">
                            <input v-model="filter.is_opportunities" @change="fetchAllOpportunities"
                                class="form-check-input" type="checkbox" id="is_opportunities">
                            <label class="form-check-label fw-bold" for="is_opportunities">
                                {{ $t('opportunity_list_table.search_is_opportunities') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <div class="w-100 p-1">
                        <div class="form-check ms-auto">
                            <input v-model="filter.is_lost" @change="fetchAllOpportunities" class="form-check-input"
                                type="checkbox" id="is_lost">
                            <label class="form-check-label fw-bold" for="is_lost">
                                {{ $t('opportunity_list_table.search_is_lost') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-5">
                            {{ $t('opportunity_list_table.client_th_text') }}
                        </div>
                        <div class="col-xl-3 col-lg-2 col-md-2 col-5">
                            {{ $t('opportunity_list_table.initiative_name_th_text') }}
                        </div>
                        <div class="col-xl-3 col-lg-2 col-md-2 col-2">
                            {{ $t('opportunity_list_table.ballpark_development_hours_th_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-2 col-md-2 col-6 d-none d-md-block">
                            {{ $t('opportunity_list_table.creation_date_th_text') }}
                        </div>
                        <div class="col-xl-1 col-lg-2 col-md-2 col-6 text-end d-none d-md-block">
                            {{ $t('opportunity_list_table.actions_th_text') }}
                        </div>
                    </div>
                </li>
                <li class="list-group-item p-1 list-group-item-action"
                    v-for="opportunity in opportunities" v-if="opportunities.length > 0" :key="opportunity.id" role="button">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-5" @click="redirectSolutionDesignPage(opportunity)">{{ opportunity.client.name }}</div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-5" @click="redirectSolutionDesignPage(opportunity)">{{ opportunity.name }}</div>
                        <div class="col-xl-3 col-lg-2 col-md-2 col-2 text-end text-md-start" @click="redirectSolutionDesignPage(opportunity)">
                            <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                {{ opportunity.ballpark_development_hours }} hrs
                            </span>
                        </div>
                        <div class="col-xl-1 col-lg-2 col-md-2 col-2 text-end text-md-start" @click="redirectSolutionDesignPage(opportunity)">
                            {{ opportunity.creation_date }}
                        </div>
                        <div class="col-xl-1 col-lg-2 col-md-2 col-12 text-start text-md-end">
                            <div class="dropdown">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="editOpportunity(opportunity)">
                                            {{ $t('opportunity_list_table.actions_edit_btn_text') }}
                                        </a>
                                    </li>
                                    <li class="small">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="showConfirmation('updateStatusLost', updateStatusLost, opportunity.id)">
                                            {{ $t('opportunity_list_table.actions_lost_status_btn_text') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li v-if="opportunities.length > 0"
                    class="list-group-item p-1 list-group-item-action border-bottom">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-xl-7 col-lg-8 col-md-8 col-10"></div>
                        <div class="col-xl-3 col-lg-2 col-md-2 col-2 fw-bold">
                            <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                {{ ballparkTotal }} hrs
                            </span>
                        </div>
                        <div class="col-xl-1 col-lg-2 col-md-2 d-none d-md-block"></div>
                        <div class="col-xl-1 col-lg-2 col-md-2 d-none d-md-block"></div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('opportunity_list_table.opportunities_not_found_text') }}
                        </div>
                    </div>
                </li>
            </ul>
            <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
                @page-changed="fetchAllOpportunities" />
        </div>
        <div id="editOpportunityModal" aria-hidden="true" aria-labelledby="editOpportunityModalLabel" class="modal fade"
            tabindex="-1">
            <EditOpportunityModalComponent ref="editOpportunityModalComponent" @pageUpdated="fetchAllOpportunities" />
        </div>
        <ConfirmationModal ref="dynamicConfirmationModal" :title="modalTitle" :message="modalMessage"
            @confirm="modalConfirmCallback" />
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import PaginationComponent from '../../components/PaginationComponent.vue';
import messageService from '../../services/messageService';
import OpportunityService from '../../services/OpportunityService';
import GlobalMessage from '../../components/GlobalMessage.vue';
import EditOpportunityModalComponent from './EditOpportunityModal.vue';
import { Modal, Tooltip } from 'bootstrap';
import showToast from '../../utils/toasts';
import eventBus from './../../eventBus';
import { mapActions } from 'vuex';
import store from '../../store';

export default {
    name: 'OpportunityListComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        PaginationComponent,
        EditOpportunityModalComponent
    },
    data() {
        return {
            opportunities: [],
            ballparkTotal: 0,
            currentPage: "",
            totalPages: "",
            errors: {},
            filterClients: [],
            filter: {
                initiative_name: "",
                client_id: "",
                is_opportunities: true,
                is_lost: false
            },
            modalTitle: '',
            modalMessage: '',
            modalConfirmCallback: null,
            showMessage: true
        }
    },
    watch: {
        'filter.is_opportunities': {
            handler(newValue, oldValue) {
                if (!newValue && !this.filter.is_lost) {
                    this.$nextTick(() => {
                        this.filter.is_opportunities = true;
                    });
                }
            },
            deep: true
        },
        'filter.is_lost': {
            handler(newValue) {
                if (!newValue && !this.filter.is_opportunities) {
                    this.$nextTick(() => {
                        this.filter.is_opportunities = true;
                    });
                }
            },
            deep: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        solutionDesignOpenInNewTab(id) {
            const routeData = this.$router.resolve({ name: 'solution-design', params: { id } });
            window.open(routeData.href, '_blank');
        },
        async getPageInitialData() {
            try {
                const response = await OpportunityService.getPageInitialData();
                this.filterClients = response.content.clients;
            } catch (error) {
                this.handleError(error);
            }
        },
        async fetchAllOpportunities(page = 1) {
            this.clearMessages();
            try {
                const params = {
                    page: page,
                    filters: this.filter
                }
                this.setLoading(true);
                const response = await OpportunityService.fetchAllOpportunities(params);
                const content = response.content;
                this.opportunities = content.opportunities.records;
                this.currentPage = content.opportunities.paginationInfo.current_page;
                this.totalPages = content.opportunities.paginationInfo.last_page;
                this.ballparkTotal = content.ballparkTotal;
                await this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        editOpportunity(opportunity) {
            this.$refs.editOpportunityModalComponent.getEditOpportunityFormData(opportunity);
            const modalElement = document.getElementById('editOpportunityModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        async updateStatusLost(id) {
            try {
                const response = await OpportunityService.updateStatusLost({ id: id });
                showToast(response.message, 'success');
                this.fetchAllOpportunities();
            } catch (error) {
                this.handleError(error);
            }
        },
        redirectSolutionDesignPage(opportunity) {
            const solutionDesignRoute = this.$router.resolve({ name: 'solution-design', params: { id: opportunity.id } });
            window.open(solutionDesignRoute.href, '_blank');
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        showConfirmation(modalType, callback, callbackParam) {
            if (modalType === 'updateStatusLost') {
                this.modalTitle = this.$t('opportunity_list_table.actions_lost_status_modal_title');
                this.modalMessage = this.$t('opportunity_list_table.actions_lost_status_modal_text');
            }

            this.modalConfirmCallback = () => callback(callbackParam);

            this.$refs.dynamicConfirmationModal.showModal();
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
        handleReloadOpportunityList() {
            this.fetchAllOpportunities();
        }
    },
    mounted() {
        this.fetchAllOpportunities();
        this.getPageInitialData();

        eventBus.$on('reloadOpportunityList', this.handleReloadOpportunityList);
        const setHeaderData = {
            page_title: this.$t('opportunity.page_title')
        }
        store.commit("setHeaderData", setHeaderData);
    },
    beforeUnmount() {
        // Hide the message when the component is unmounted
        this.showMessage = false;

        eventBus.$off('reloadOpportunityList', this.handleReloadOpportunityList);
    }
}
</script>
