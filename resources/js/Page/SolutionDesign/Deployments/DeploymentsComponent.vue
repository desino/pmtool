<template>
    <GlobalMessage />
    <div class="app-content mt-3">
        <div class="row w-100 mb-3 align-items-center">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <input v-model="filter.name" :placeholder="$t('deployments.filter.deployment_name')"
                    class="form-control" type="text" @keyup="getDeployments">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <input v-model="filter.ticket_name" :placeholder="$t('deployments.filter.ticket_name')"
                    class="form-control" type="text" @keyup="getDeployments">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <multiselect v-model="filter.functionalities" ref="multiselect" :multiple="true"
                    :options="functionalities" :searchable="true" deselect-label="" label="display_name"
                    :placeholder="$t('ticket.filter.functionalities_placeholder')" track-by="id"
                    @select="getDeployments" @Remove="getDeployments">
                </multiselect>
            </div>
        </div>
        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="font-weight-bold bg-desino text-white rounded-top list-group-item">
                <div class="row w-100">
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('deployments.list.column_request_date') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('deployments.list.column_deployment_date') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('deployments.list.column_name') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('deployments.list.column_tickets_included') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2">
                        {{ $t('deployments.list.column_status') }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 fw-bold py-2 text-end">
                        {{ $t('deployments.list.column_actions') }}
                    </div>
                </div>
            </li>

            <li v-for="(deployment, index) in deployments" v-if="deployments.length > 0" :key="index"
                class="border list-group-item">
                <div class="row w-100 align-items-center">
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
                        {{ deployment?.status_name }}
                    </div>
                    <div class="col-lg-2 col-md-6 col-6 text-end">
                        <!-- target="_blank" -->
                        <router-link
                            :to="{ name: 'tasks', params: { id: deployment?.initiative_id }, query: { deployment_id: deployment?.id } }"
                            class="text-success me-2">
                            <i class="bi bi-box-arrow-up-right fw-bold"></i>
                        </router-link>
                        <a href="javascript:" :title="$t('deployments.list.column.action.download_release_note_text')"
                            @click="downloadReleaseNotes(deployment)" class="text-info me-2">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                        <a href="javascript:" :title="$t('deployments.list.column.action.download_test_results_text')"
                            @click="downloadTestResults(deployment)" class="text-secondary">
                            <i class="bi bi-file-pdf"></i>
                        </a>
                    </div>
                </div>
            </li>
            <li v-else class="list-group-item border p-4">
                <div class="col h4 fw-bold text-center">{{ $t('deployments.list.not_record_found') }}
                </div>
            </li>
        </ul>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="getDeployments" />
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
            errors: {},
            showMessage: true
        }
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
                this.setLoading(false);
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
                const { content: { functionalities } } = await DeploymentService.getInitiativeDataForDeployments(params);
                this.functionalities = functionalities;
                this.setLoading(false);
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
            page_title: this.$t('deployments.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.fetchData();
        next();
    },
}
</script>