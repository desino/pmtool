<template>
    <div class="card border-0">
        <div class="card-header bg-desino text-white text-center fw-bold fs-4 p-2">
            {{ $t('home.deployment_center.title') }}
        </div>
        <div class="card-body p-0">
            <div class="card border-0 mb-3">
                <div class="card-body px-0 py-1">
                    <ul class="list-group list-group-flush w-100">
                        <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                            {{ $t('home.deployment_center.production_deployment.title') }}
                        </li>
                        <li class="list-group-item list-group-item-action p-1" v-if="productionDeployments.length > 0"
                            v-for="productionDeployment in productionDeployments" :key="productionDeployment.id"
                            @click="openProductionDeploymentModal(productionDeployment)" role="button">
                            <div class="row g-1 align-items-center">
                                <div class="col-9 col-lg-10">
                                    {{ productionDeployment?.client_name }} -
                                    {{ productionDeployment?.name }}
                                </div>
                                <div class="col-3 col-lg-2 text-end">
                                    <span class="badge bg-desino">
                                        {{ productionDeployment?.tickets_count }}
                                        <span class="small">
                                            {{ $t('home.deployment_center.production_deployment.tickets.text') }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li v-else class="list-group-item p-1">
                            <div class="w-100 fst-italic small text-secondary">
                                {{ $t('home.deployment_center.production_deployment.record_does_not_exist') }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card border-0 mb-3">
                <div class="card-body px-0 py-1">
                    <ul class="list-group list-group-flush w-100">
                        <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                            {{ $t('home.deployment_center.acceptance_deployment.title') }}
                        </li>
                        <li class="list-group-item list-group-item-action p-1" v-if="acceptanceDeployments.length > 0"
                            v-for="acceptanceDeployment in acceptanceDeployments" :key="acceptanceDeployment.id"
                            @click="openAcceptanceDeploymentModal(acceptanceDeployment)" role="button">
                            <div class="row g-1 align-items-center">
                                <div class="col-9 col-lg-10">
                                    {{ acceptanceDeployment?.client?.name }} - {{ acceptanceDeployment?.name }}
                                </div>
                                <div class="col-3 col-lg-2 text-end">
                                    <span class="badge bg-desino">
                                        {{ acceptanceDeployment?.tickets_count}}
                                        <span class="small">
                                            {{ $t('home.deployment_center.acceptance_deployment.tickets.text') }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li v-else class="list-group-item p-1">
                            <div class="w-100 fst-italic small text-secondary">
                                {{ $t('home.deployment_center.acceptance_deployment.record_does_not_exist') }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card border-0">
                <div class="card-body px-0 py-1">
                    <ul class="list-group list-group-flush w-100">
                        <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                            {{ $t('home.deployment_center.test_deployment.title') }}
                        </li>
                        <li class="list-group-item list-group-item-action p-1" v-if="testDeployments.length > 0"
                            v-for="testDeployment in testDeployments" :key="testDeployment.id"
                            @click="openTestDeploymentModal(testDeployment)" role="button">
                            <div class="row g-1 align-items-center">
                                <div class="col-9 col-lg-10">
                                    {{ testDeployment?.client_initiative_name }}
                                </div>
                                <div class="col-3 col-lg-2 text-end">
                                    <span class="badge bg-desino">
                                        {{ testDeployment?.tickets_count }}
                                        <span class="small">
                                            {{ $t('home.deployment_center.test_deployment.tickets.text') }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li v-else class="list-group-item p-1">
                            <div class="w-100 fst-italic small text-secondary">
                                {{ $t('home.deployment_center.test_deployment.record_does_not_exist') }}
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="testDeploymentTicketsModal" aria-hidden="true" aria-labelledby="testDeploymentTicketsModalLabel"
        class="modal fade" tabindex="-1">
        <TestDeploymentTicketsModalComponent ref="testDeploymentTicketsModalComponent"
            @pageUpdated="getDeploymentCenterData()" />
    </div>
    <div id="acceptanceDeploymentTicketsModal" aria-hidden="true"
        aria-labelledby="acceptanceDeploymentTicketsModalLabel" class="modal fade" tabindex="-1">
        <AcceptanceDeploymentTicketsModalComponent ref="acceptanceDeploymentTicketsModalComponent"
            @pageUpdated="getDeploymentCenterData()" />
    </div>
    <div id="productionDeploymentTicketsModal" aria-hidden="true"
        aria-labelledby="productionDeploymentTicketsModalLabel" class="modal fade" tabindex="-1">
        <ProductionDeploymentTicketsModalComponent ref="productionDeploymentTicketsModalComponent"
            @pageUpdated="getDeploymentCenterData()" />
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import { mapActions } from 'vuex';
import eventBus from '../../../eventBus';
import messageService from '../../../services/messageService';
import showToast from '../../../utils/toasts';
import DeploymentCenterService from '../../../services/Home/DeploymentCenterService';
import TestDeploymentTicketsModalComponent from './TestDeploymentTicketsModalComponent.vue';
import { Modal } from 'bootstrap';
import AcceptanceDeploymentTicketsModalComponent from './AcceptanceDeploymentTicketsModalComponent.vue';
import ProductionDeploymentTicketsModalComponent from './ProductionDeploymentTicketsModalComponent.vue';
export default {
    name: 'DeploymentCentreComponent',
    mixins: [globalMixin],
    components: {
        TestDeploymentTicketsModalComponent,
        AcceptanceDeploymentTicketsModalComponent,
        ProductionDeploymentTicketsModalComponent
    },
    data() {
        return {
            productionDeployments: [],
            testDeployments: [],
            acceptanceDeployments: [],
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getDeploymentCenterData() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const { content: { testDeploymentInitiatives, acceptanceDeploymentInitiative, productionDeploymentInitiatives } } = await DeploymentCenterService.getDeploymentCenterData();
                this.testDeployments = testDeploymentInitiatives;
                this.acceptanceDeployments = acceptanceDeploymentInitiative;
                this.productionDeployments = productionDeploymentInitiatives;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        openProductionDeploymentModal(productionDeployment) {
            this.$refs.productionDeploymentTicketsModalComponent.getProductionDeploymentTicketsModalData(productionDeployment);
            const modalElement = document.getElementById('productionDeploymentTicketsModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        openAcceptanceDeploymentModal(acceptanceDeployment) {
            this.$refs.acceptanceDeploymentTicketsModalComponent.getAcceptanceDeploymentTicketsModalData(acceptanceDeployment);
            const modalElement = document.getElementById('acceptanceDeploymentTicketsModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        openTestDeploymentModal(testDeployment) {
            this.$refs.testDeploymentTicketsModalComponent.getTestDeploymentTicketsModalData(testDeployment);
            const modalElement = document.getElementById('testDeploymentTicketsModal');
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
        this.clearMessages();
        this.getDeploymentCenterData();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
