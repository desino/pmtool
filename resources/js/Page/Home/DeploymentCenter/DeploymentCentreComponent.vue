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
                            <div class="card-header text-center bg-warning text-white">
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
                                N/A
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header text-center">
                                {{ $t('home.deployment_center.test_deployment.title') }}
                            </div>
                            <div class="card-body">
                                <ul v-if="testDeployments.length > 0" v-for="testDeployment in testDeployments"
                                    :key="testDeployment.id" class="list-group list-group-flush"
                                    @click="openTestDeploymentModal(testDeployment)">
                                    <li class="list-group-item" role="button">
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
            <TestDeploymentTicketsModalComponent ref="testDeploymentTicketsModalComponent" />
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
export default {
    name: 'DeploymentCentreComponent',
    mixins: [globalMixin],
    components: {
        GlobalMessage,
        TestDeploymentTicketsModalComponent,
    },
    data() {
        return {
            testDeployments: [],
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
                const { content: { testDeploymentInitiatives } } = await DeploymentCenterService.getDeploymentCenterData();
                this.testDeployments = testDeploymentInitiatives;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
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