<template>
    <div class="modal-dialog">
        <form @submit.prevent="submitTestDeploymentTicket">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testDeploymentTicketsModalLabel">{{
                        $t('home.deployment_center.test_deployment.ticket_modal.title')
                    }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <ul class="list-group">
                        <li class="list-group-item fw-bold bg-desino text-white">
                            <div class="row">
                                <div class="col-md-1">
                                    <input class="form-check-input" type="checkbox"
                                        v-model="isChkAllTestDeploymentTickets"
                                        @change="handleSelectAllTestDeploymentTickets">
                                </div>
                                <div class="col-md-8">
                                    {{ $t('home.deployment_center.test_deployment.ticket_modal.li.name.text') }}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item" v-for="ticket in ticketList" :key="ticket.id">
                            <div class="row">
                                <div class="col-md-1">
                                    <input class="form-check-input" type="checkbox"
                                        :id="'chk_test_deployment_ticket_' + ticket.id" v-model="ticket.isChecked"
                                        @change="handleSelectTestDeploymentTicket(ticket)">
                                </div>
                                <div class="col-md-8" :for="'chk_test_deployment_ticket_' + ticket.id">
                                    {{ ticket?.composed_name }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-desino"
                        :disabled="selectedTestDeploymentTickets.length > 0 ? false : true">{{
                            $t('home.deployment_center.test_deployment.ticket_modal.submit_but.text') }}</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import messageService from '../../../services/messageService';
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import { mapActions } from 'vuex';
import eventBus from "@/eventBus.js";
import GlobalMessage from '../../../components/GlobalMessage.vue';
import DeploymentCenterService from '../../../services/Home/DeploymentCenterService';
export default {
    name: 'EditOpportunityModal',
    components: {
        GlobalMessage,
    },
    data() {
        return {
            isChkAllTestDeploymentTickets: false,
            ticketList: [],
            selectedTestDeploymentTickets: [],
            initiativeId: "",
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getTestDeploymentTicketsModalData(testDeployment) {
            this.selectedTestDeploymentTickets = [];
            this.isChkAllTestDeploymentTickets = false;
            this.initiativeId = testDeployment.id;
            const passData = {
                initiative_id: testDeployment.id,
            }
            const { content: { tickets } } = await DeploymentCenterService.getTestDeploymentTicketsModalData(passData);
            this.ticketList = tickets.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
        },
        async submitTestDeploymentTicket() {
            this.clearMessages();
            if (this.selectedTestDeploymentTickets.length == 0) {
                return false;
            }
            this.$swal({
                title: this.$t('home.deployment_center.test_deployment.ticket_modal.submit.alert.text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'btn-desino',
                },
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const params = {
                            initiative_id: this.initiativeId,
                            ticketIds: this.selectedTestDeploymentTickets,
                        }
                        await this.setLoading(true);
                        const { message } = await DeploymentCenterService.submitTestDeploymentTicket(params);
                        this.hideTestDeploymentModal();
                        showToast(message, 'success');
                        await this.setLoading(false);
                        this.$emit('pageUpdated');
                    } catch (error) {
                        this.handleError(error);
                        this.resetTestDeploymentTicketList();
                    }
                } else {
                    this.resetTestDeploymentTicketList();
                }
            }).catch(() => {
                this.resetTestDeploymentTicketList();
            });
        },
        handleSelectAllTestDeploymentTickets() {
            this.selectedTestDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => {
                ticket.isChecked = this.isChkAllTestDeploymentTickets;
                if (this.isChkAllTestDeploymentTickets) {
                    this.selectedTestDeploymentTickets.push(ticket.id);
                }
                return ticket;
            });
        },
        handleSelectTestDeploymentTicket(ticket) {
            if (ticket.isChecked) {
                if (!this.selectedTestDeploymentTickets.includes(ticket.id)) {
                    this.selectedTestDeploymentTickets.push(ticket.id);
                }
            } else {
                this.selectedTestDeploymentTickets = this.selectedTestDeploymentTickets.filter(id => id !== ticket.id);
            }
            this.isChkAllTestDeploymentTickets = this.ticketList.every(ticket => ticket.isChecked);
        },
        resetTestDeploymentTicketList() {
            this.selectedTestDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
            this.isChkAllTestDeploymentTickets = false;
        },
        hideTestDeploymentModal() {
            const modalElement = document.getElementById('testDeploymentTicketsModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    this.setLoading(false);
                    modal.hide();
                }
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
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
