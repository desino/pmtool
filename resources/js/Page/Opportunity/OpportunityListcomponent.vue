<template>
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ $t('opportunity.page_title') }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a class="text-decoration-none" href="javascript:void(0)">{{
                            $t('Dashboard') }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="row mb-3">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <input v-model="filter.initiative_name"
                    :placeholder="$t('opportunity_list_table.search_placeholder_initiative_name')" class="form-control"
                    type="text" @keyup="fetchAllOpportunities">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <select id="client_id" v-model="filter.client_id" class="form-select" @change="fetchAllOpportunities">
                    <option value="">{{ $t('opportunity_list_table.search_placeholder_client_id') }}</option>
                    <option v-for="client in filterClients" :key="client.id" :value="client.id">{{ client.name }}
                    </option>
                </select>
            </div>
        </div>
        <div class="list-group-item mx-2 mb-3 mt-2">
            <div class="row justify-content-between font-weight-bold bg-desino text-white rounded-top">
                <div class="col-lg-4 col-md-6 col-6 fw-bold py-2">{{ $t('opportunity_list_table.client_th_text') }}
                </div>
                <div class="col-lg-3 col-md-6 col-6 fw-bold py-2">{{
                    $t('opportunity_list_table.initiative_name_th_text')
                }}
                </div>
                <div class="col-lg-3 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                    {{ $t('opportunity_list_table.ballpark_development_hours_th_text') }}
                </div>
                <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 d-none d-lg-block">
                    {{ $t('opportunity_list_table.actions_th_text') }}
                </div>
            </div>
            <div v-for="opportunity in opportunities" v-if="opportunities.length > 0" :key="opportunity.id">
                <div class="row border-desino border p-2">
                    <div class="col-lg-4 col-md-6 col-6">{{ opportunity.client.name }}</div>
                    <div class="col-lg-3 col-md-6 col-6">{{ opportunity.name }}</div>
                    <div class="col-lg-3 col-md-6 col-8 text-center text-lg-start">
                        <span class="d-block d-lg-none fw-bold bg-desino mt-2 p-0 text-white text-center rounded-top">
                            {{
                                $t('opportunity_list_table.ballpark_development_hours_th_text')
                            }} </span>
                        {{ opportunity.ballpark_development_hours }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-4">
                        <span class="d-block d-lg-none fw-bold bg-light-subtle mt-2 text-white text-center"> {{
                            $t('opportunity_list_table.actions_th_text')
                        }} </span>
                        <a :title="$t('opportunity_list_table.actions_edit_tooltip')" class="text-desino me-2"
                            href="javascript:" @click="editOpportunity(opportunity)">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a :title="$t('opportunity_list_table.actions_lost_status_tooltip')" class="text-warning me-2"
                            href="javascript:" @click="updateStatusLostConfirmed(opportunity.id)">
                            <i class="bi bi-hand-thumbs-down"></i>
                        </a>
                        <router-link :title="$t('opportunity_list_table.actions_redirect_to_solution_design_tooltip')"
                            :to="{ name: 'solution-design', params: { id: opportunity.id } }" class="text-success me-2">
                            <i class="bi bi-box-arrow-right"></i>
                        </router-link>
                    </div>
                </div>
            </div>
            <div v-if="opportunities.length > 0" class="row border p-2">
                <div class="col-lg-4 col-md-6 col-6"></div>
                <div class="col-lg-3 col-md-6 col-6"></div>
                <div class="col-lg-3 fw-bold col-md-6 col-8">{{ ballparkTotal }}</div>
                <div class="col-lg-2 text-end col-md-6 col-4">
                </div>
            </div>
            <div v-else class="list-group-item row border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('opportunity_list_table.opportunities_not_found_text') }}
                </div>
            </div>
        </div>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="fetchAllOpportunities" />
        <div id="editOpportunityModal" aria-hidden="true" aria-labelledby="editOpportunityModalLabel" class="modal fade"
            tabindex="-1">
            <EditOpportunityModalComponent ref="editOpportunityModalComponent" @pageUpdated="fetchAllOpportunities" />
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import PaginationComponent from '../../components/PaginationComponent.vue';
import messageService from '../../services/messageService';
import OpportunityService from '../../services/OpportunityService';
import GlobalMessage from '../../components/GlobalMessage.vue';
import EditOpportunityModalComponent from './EditOpportunityModal.vue';
import { Modal } from 'bootstrap';
import showToast from '../../utils/toasts';
import eventBus from './../../eventBus';
import { mapActions } from 'vuex';

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
            },
            showMessage: true
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
                this.setLoading(false);
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
        updateStatusLostConfirmed(id) {
            this.$swal({
                title: this.$t('opportunity_list_table.actions_lost_status_modal_title'),
                text: this.$t('opportunity_list_table.actions_lost_status_modal_text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: this.$t('opportunity_list_table.actions_lost_status_modal_confirm_button_text')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.updateStatusLost(id);
                }
            })
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
    },
    beforeUnmount() {
        // Hide the message when the component is unmounted
        this.showMessage = false;

        eventBus.$off('reloadOpportunityList', this.handleReloadOpportunityList);
    }
}
</script>
