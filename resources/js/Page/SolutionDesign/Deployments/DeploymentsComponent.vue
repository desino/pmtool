<template>
    <GlobalMessage />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-0 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="w-100 p-1">
                        <input v-model="filter.name" :placeholder="$t('deployments.filter.deployment_name')"
                            class="form-control" type="text" @keyup="getDeployments">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="w-100 p-1">
                        <input v-model="filter.ticket_name" :placeholder="$t('deployments.filter.ticket_name')"
                            class="form-control" type="text" @keyup="getDeployments">
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="w-100 p-1">
                        <multiselect v-model="filter.functionalities" ref="multiselect" :multiple="true"
                            :options="functionalities" :searchable="true" deselect-label="" label="display_name"
                            :placeholder="$t('ticket.filter.functionalities_placeholder')" track-by="id"
                            @select="getDeployments" @Remove="getDeployments">
                        </multiselect>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('deployments.list.column_request_date') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('deployments.list.column_deployment_date') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('deployments.list.column_name') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('deployments.list.column_tickets_included') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ $t('deployments.list.column_status') }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6 text-end">
                            {{ $t('deployments.list.column_actions') }}
                        </div>
                    </div>
                </li>
                <li v-for="(deployment, index) in deployments" v-if="deployments.length > 0" :key="index"
                    class="list-group-item p-1 list-group-item-action">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ deployment?.request_date_format }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ deployment?.deployment_date_format }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ deployment?.name }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            {{ deployment?.tickets_count }}
                        </div>
                        <div class="col-lg-2 col-md-6 col-6">
                            <i v-if="deployment?.status" class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        <div class="col-lg-2 col-md-6 col-6 text-end">
                            <div class="dropdown">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small pb-1" v-if="user?.is_admin">
                                        <router-link v-if="user?.is_admin" target="_blank"
                                            :to="{ name: 'tasks', params: { id: deployment?.initiative_id }, query: { deployment_id: deployment?.id } }"
                                            class="btn btn-sm btn-desino w-100 small">
                                            {{ $t('deployments.list.column.action.view_deployment_tickets') }}
                                        </router-link>
                                    </li>
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="downloadReleaseNotes(deployment)">
                                            {{ $t('deployments.list.column.action.download_release_note_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-warning w-100 small" href="javascript:" @click="downloadTestResults(deployment)" >
                                            {{ $t('deployments.list.column.action.download_test_results_text') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('deployments.list.not_record_found') }}
                        </div>
                    </div>
                </li>
            </ul>
            <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
                @page-changed="getDeployments" />
        </div>
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import messageService from '../../../services/messageService';
import eventBus from '../../../eventBus';
import DeploymentService from '../../../services/DeploymentsService';
import PaginationComponent from '../../../components/PaginationComponent.vue';
import Multiselect from "vue-multiselect";
import store from '../../../store';
import { Modal, Tooltip } from 'bootstrap';
export default {
    name: 'DeploymentsComponent',
    mixins: [globalMixin],
    components: {
        Multiselect,
        GlobalMessage,
        PaginationComponent
    },
    data() {
        return {
            initiative_id: this.$route.params.id,
            currentInitiative: store.getters.currentInitiative,
            functionalities: [],
            filter: {
                name: "",
                ticket_name: "",
                functionalities: [],
            },
            deployments: [],
            currentPage: "",
            totalPages: "",
            initiativeData: {},
            errors: {},
            showMessage: true
        }
    },
    computed: {
        ...mapGetters(['user', 'currentInitiative']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            try {
                this.clearMessages();
                await Promise.all([
                    this.getDeployments(),
                    this.getInitiativeDataForDeployments(),
                    this.$route.name == 'deployments' ? eventBus.$emit('selectHeaderInitiativeId', this.initiative_id) : '',
                ]);
            } catch (error) {
                this.handleError(error);
            }
        },
        async getDeployments(page = 1) {
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    page: page,
                    initiative_id: this.initiative_id,
                    filters: this.filter
                }
                const { content: { data, current_page, last_page }, meta_data } = await DeploymentService.getDeployments(params);
                this.deployments = data;
                this.currentPage = current_page;
                this.totalPages = last_page;
                await this.setLoading(false);
                this.initializeTooltips();
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitiativeDataForDeployments() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    initiative_id: this.initiative_id,
                }
                const { content: { functionalities, initiative } } = await DeploymentService.getInitiativeDataForDeployments(params);
                this.functionalities = functionalities;
                this.initiativeData = initiative;
                this.setLoading(false);
                this.setPageHeader();
            } catch (error) {
                this.handleError(error);
            }
        },
        async downloadReleaseNotes(release) {
            this.clearMessages();
            try {
                this.setLoading(true);
                const currentInitiative = await store.getters.currentInitiative;
                const passData = {
                    initiative_id: this.initiative_id,
                    release_id: release?.id,
                }
                const response = await DeploymentService.downloadReleaseNotes(passData);

                const blob = new Blob([response.data], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Release Notes - ' + currentInitiative?.name + ' - ' + release?.name + '.pdf';
                link.click();
                this.setLoading(false);
            } catch (error) {
                error.message = this.$t('deployments.download_release_note.error_message');
                this.handleError(error);
            }
        },
        async downloadTestResults(release) {
            this.clearMessages();
            try {
                this.setLoading(true);
                const currentInitiative = await store.getters.currentInitiative;
                const passData = {
                    initiative_id: this.initiative_id,
                    release_id: release?.id,
                }
                const response = await DeploymentService.downloadTestResults(passData);

                const blob = new Blob([response.data], { type: 'application/pdf' });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Test Case - ' + currentInitiative?.name + ' - ' + release?.name + '.pdf';
                link.click();
                this.setLoading(false);
            } catch (error) {
                error.message = this.$t('deployments.download_release_note.error_message');
                this.handleError(error);
            }
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        setPageHeader() {
            const setHeaderData = {
                page_title: this.$t('deployments.page_title') + ' - ' + this.initiativeData?.name,
            }
            store.commit("setHeaderData", setHeaderData);
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
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.fetchData();
        next();
    },
}
</script>
