<template>
    <h1 class="primary">{{ $t('opportunity.page_title') }}</h1>
    <GlobalMessage v-if="showMessage" />
    <div class="row mb-3 mt-5 justify-content-end">
        <div class="col-12 col-md-3 mb-2 mb-md-0">
            <input type="text" @keyup="fetchAllOpportunities" class="form-control" :placeholder="$t('opportunity_list_table.search_placeholder_initiative_name')" v-model="filter.initiative_name">
        </div>
        <div class="col-12 col-md-3 mb-2 mb-md-0">
            <select v-model="filter.client_id" @change="fetchAllOpportunities" id="client_id" class="form-select">
                <option value="">{{ $t('opportunity_list_table.search_placeholder_client_id') }}</option>
                <option v-for="client in filterClients" :key="client.id" :value="client.id">{{ client.name }}</option>
            </select>
        </div>
        <!-- <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100" type="button" @click="fetchAllOpportunities" @keydown="fetchAllOpportunities">{{ $t('opportunity_list_table.search_button_text') }}</button>
        </div> -->
    </div>
    <ul class="list-group mb-3 mt-2">
        <li class="list-group-item d-flex justify-content-between font-weight-bold">
            <span class="col fw-bold">{{ $t('opportunity_list_table.client_th_text') }}</span>
            <span class="col fw-bold">{{ $t('opportunity_list_table.initiative_name_th_text') }}</span>
            <span class="col fw-bold text-center">{{ $t('opportunity_list_table.ballpark_development_hours_th_text') }}</span>
            <span class="col fw-bold text-end">{{ $t('opportunity_list_table.actions_th_text') }}</span>
        </li>
        <li v-if="opportunities.length > 0" v-for="opportunity in opportunities" :key="opportunity.id" class="list-group-item d-flex justify-content-between align-items-center">
            <span class="col">{{ opportunity.client.name }}</span>
            <span class="col">{{ opportunity.name }}</span>
            <span class="col text-center">{{ opportunity.ballpark_development_hours }}</span>
            <span class="col text-end">
                <a href="javascript:" class="text-primary me-2" :title="$t('opportunity_list_table.actions_edit_tooltip')" @click="editOpportunity(opportunity)">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="javascript:" class="text-warning me-2" :title="$t('opportunity_list_table.actions_lost_status_tooltip')" @click="updateStatusLostConfirmed(opportunity.id)">
                    <i class="bi bi-hand-thumbs-down-fill"></i>
                </a>
            </span>
        </li>
        <li v-if="opportunities.length > 0" class="list-group-item d-flex justify-content-between align-items-center">
            <span class="col"></span>
            <span class="col"></span>
            <span class="col text-center fw-bold">{{ ballparkTotal }}</span>
            <span class="col text-end">
            </span>
        </li>
        <li v-else class="list-group-item d-flex justify-content-between align-items-center">
            <span class="col fw-bold text-center">{{ $t('opportunity_list_table.opportunities_not_found_text') }}</span>
        </li>
    </ul>
    <PaginationComponent
        :currentPage="Number(currentPage)"
        :totalPages="Number(totalPages)"
        @page-changed="fetchAllOpportunities"
    />
    <div class="modal fade" id="editOpportunityModal" tabindex="-1" aria-labelledby="editOpportunityModalLabel" aria-hidden="true">
        <EditOpportunityModalComponent ref="editOpportunityModalComponent" @pageUpddated="fetchAllOpportunities"/>
    </div>
</template>

<script>
    import globalMixin from '@/globalMixin';
    import PaginationComponent from '../../components/PaginationComponent.vue';
    import messageService from '../../services/messageService';
    import OpportunityService from '../../services/OpportunityService';
    import { mapState } from 'vuex/dist/vuex.cjs.js';
    import GlobalMessage from '../../components/GlobalMessage.vue';
    import EditOpportunityModalComponent from './EditOpportunityModal.vue';
    import { Modal } from 'bootstrap';
    import showToast from '../../utils/toasts';

    export default {
        name: 'OpportunityListComponjent',
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
            ...mapState(['loading']),
            async getPageInitialData() {
                try {
                    const response = await OpportunityService.getPageInitialData();
                    this.filterClients = response.content.clients;                    
                } catch (error) {
                    this.handleError(error);
                }
            },
            async fetchAllOpportunities(page=1) {
                this.clearMessages();
                try {
                    const params = {
                        page: page,
                        filter: this.filter
                    }
                    // await this.setLoading(true);
                    const response = await OpportunityService.fetchAllOpportunites(params);
                    const content = response.content;
                    this.opportunities = content.oppertunities.records;
                    this.currentPage = content.oppertunities.paginationInfo.current_page;
                    this.totalPages = content.oppertunities.paginationInfo.last_page;
                    this.ballparkTotal = content.ballparkTotal;
                    // await this.setLoading(false);
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
                    // icon: 'warning',
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
                    const response = await OpportunityService.updateStatusLost({id: id});
                    showToast(response.data.message, 'success');
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
            },
            clearMessages() {
                this.errors = {};
                messageService.clearMessage();
            },
        },
        mounted() {
            this.fetchAllOpportunities();
            this.getPageInitialData();
        },
        beforeUnmount() {
        // Hide the message when the component is unmounted
            this.showMessage = false;
        }
    }
</script>