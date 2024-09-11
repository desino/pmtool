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
                                    <input class="form-check-input" type="checkbox" id="chk_all_tickets"
                                        v-model="isChkAllTestDeploymentTickets"
                                        @change="handleSelectAllTestDeploymentTickets">
                                </div>
                                <div class="col-md-8">
                                    Name
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item" v-for="ticket in ticketList" :key="ticket.id">
                            <div class="row">
                                <div class="col-md-1">
                                    <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + ticket.id"
                                        v-model="ticket.isChecked" @change="handleSelectTestDeploymentTicket(ticket)">
                                </div>
                                <div class="col-md-8">
                                    {{ ticket?.name }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-desino bg-desino text-light">{{
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
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getTestDeploymentTicketsModalData(testDeployment) {
            this.selectedTestDeploymentTickets = [];
            this.isChkAllTestDeploymentTickets = false;
            const passData = {
                initiative_id: testDeployment.id,
            }
            const { content: { tickets } } = await DeploymentCenterService.getTestDeploymentTicketsModalData(passData);
            this.ticketList = tickets.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
            // this.$emit('pageUpdated');
        },
        submitTestDeploymentTicket() {
            console.log('sdfsdfsdf :: ', this.selectedTestDeploymentTickets);
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
        hideModal() {
            const modalElement = document.getElementById('editOpportunityModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        }
    },
    mounted() {
        this.clearMessages();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
