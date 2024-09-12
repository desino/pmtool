<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content mt-3">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-desino text-white text-center">
                        {{ $t('home.deployment_center.title') }}
                    </div>
                    <div class="card-body">
                        <div class="card mb-3">
                            <div class="card-header text-center">
                                {{ $t('home.deployment_center.production_deployment.title') }}
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header text-center">
                                {{ $t('home.deployment_center.acceptance_deployment.title') }}
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item" v-if="acceptanceDeployments.length > 0"
                                        v-for="acceptanceDeployment in acceptanceDeployments"
                                        :key="acceptanceDeployment.id"
                                        @click="openAcceptanceDeploymentModal(acceptanceDeployment)" role="button">
                                        <div class="row">
                                            <div class="col-md-8">
                                                {{ acceptanceDeployment?.client?.name }} -
                                                {{ acceptanceDeployment?.name }}
                                            </div>
                                            <div class="col-md-4">
                                                <h6>
                                                    <span class="badge bg-desino">{{ acceptanceDeployment?.tickets_count
                                                        }}
                                                        <span class="small">{{
                                                            $t('home.deployment_center.test_deployment.tickets.text')
                                                        }}</span>
                                                    </span>
                                                </h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li v-else class="list-group-item">
                                        <div class="row col-md-12">
                                            {{ $t('home.deployment_center.test_deployment.record_does_not_exist') }}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header text-center">
                                {{ $t('home.deployment_center.test_deployment.title') }}
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item" v-if="testDeployments.length > 0"
                                        v-for="testDeployment in testDeployments" :key="testDeployment.id"
                                        @click="openTestDeploymentModal(testDeployment)" role="button">
                                        <div class="row">
                                            <div class="col-md-8">
                                                {{ testDeployment?.client?.name }} - {{ testDeployment?.name }}
                                            </div>
                                            <div class="col-md-4">
                                                <h6>
                                                    <span class="badge bg-desino">{{ testDeployment?.tickets_count }}
                                                        <span class="small">{{
                                                            $t('home.deployment_center.test_deployment.tickets.text')
                                                        }}</span>
                                                    </span>
                                                </h6>
                                            </div>
                                        </div>
                                    </li>
                                    <li v-else class="list-group-item">
                                        <div class="row col-md-12">
                                            {{ $t('home.deployment_center.test_deployment.record_does_not_exist') }}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">

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
    </div>
</template>

<script>
import globalMixin from '@/globalMixin';
import GlobalMessage from '../../../components/GlobalMessage.vue';
import { mapActions } from 'vuex';
import eventBus from '../../../eventBus';
import messageService from '../../../services/messageService';
import showToast from '../../../utils/toasts';
import DeploymentCenterService from '../../../services/Home/DeploymentCenterService';
import TestDeploymentTicketsModalComponent from './TestDeploymentTicketsModalComponent.vue';
import { Modal } from 'bootstrap';
import AcceptanceDeploymentTicketsModalComponent from './AcceptanceDeploymentTicketsModalComponent.vue';
export default {
    name: 'DeploymentCentreComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        TestDeploymentTicketsModalComponent,
        AcceptanceDeploymentTicketsModalComponent
    },
    data() {
        return {
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
                const { content: { testDeploymentInitiatives, acceptanceDeploymentInitiative } } = await DeploymentCenterService.getDeploymentCenterData();
                this.testDeployments = testDeploymentInitiatives;
                this.acceptanceDeployments = acceptanceDeploymentInitiative;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
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