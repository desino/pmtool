<template>
    <div class="modal-dialog">
        <form @submit.prevent="submitAcceptanceDeploymentTicket">
            <div class="modal-content border-0">
                <div class="modal-header modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="acceptanceDeploymentTicketsModalLabel"
                        v-html="formattedModalTitleACCDeployment()"></h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <ul class="list-group">
                        <li class="list-group-item fw-bold bg-desino text-white">
                            <div class="row w-100">
                                <div class="col-md-1">
                                    <input class="form-check-input" type="checkbox" id="chk_all_tickets"
                                        v-model="isChkAllAcceptanceDeploymentTickets"
                                        @change="handleSelectAllAcceptanceDeploymentTickets">
                                </div>
                                <div class="col-md-11">
                                    {{ $t('home.deployment_center.acceptance_deployment.ticket_modal.li.name.text') }}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item list-group-item-action" v-for="ticket in ticketList"
                            :key="ticket.id">
                            <div class="row w-100">
                                <div class="col-md-1">
                                    <input class="form-check-input" type="checkbox"
                                        :id="'chk_acceptance_deployment_ticket_' + ticket.id" v-model="ticket.isChecked"
                                        @change="handleSelectAcceptanceDeploymentTicket(ticket)">
                                </div>
                                <div class="col-md-11" :for="'chk_acceptance_deployment_ticket_' + ticket.id">
                                    {{ ticket?.composed_name }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1">
                        <div class="col-4 col-md-6 col-lg-6">
                            <button type="submit" class="btn btn-desino w-100 border-0"
                                :disabled="selectedAcceptanceDeploymentTickets.length > 0 ? false : true">{{
                                    $t('home.deployment_center.acceptance_deployment.ticket_modal.submit_but.text')
                                }}</button>
                        </div>
                        <div class="col-4 col-md-6 col-lg-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
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
            isChkAllAcceptanceDeploymentTickets: false,
            ticketList: [],
            selectedAcceptanceDeploymentTickets: [],
            initiative: {},
            initiativeId: "",
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        formattedModalTitleACCDeployment() {
            const title = this.$t('home.deployment_center.acceptance_deployment.ticket_modal.title', { 'INITIATIVE_NAME': this.initiative?.name });
            return title.replace(this.initiative?.name, `<span class='badge bg-secondary'>${this.initiative?.name}</span>`);
        },
        async getAcceptanceDeploymentTicketsModalData(acceptanceDeployment) {
            this.selectedAcceptanceDeploymentTickets = [];
            this.isChkAllAcceptanceDeploymentTickets = false;
            this.initiativeId = acceptanceDeployment.id;
            const passData = {
                initiative_id: acceptanceDeployment.id,
            }
            const { content: { tickets, initiative } } = await DeploymentCenterService.getAcceptanceDeploymentTicketsModalData(passData);
            this.initiative = initiative;
            this.ticketList = tickets.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
        },
        async submitAcceptanceDeploymentTicket() {
            this.clearMessages();
            if (this.selectedAcceptanceDeploymentTickets.length == 0) {
                return false;
            }
            this.$swal({
                title: this.$t('home.deployment_center.acceptance_deployment.ticket_modal.submit.alert.text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'bg-desino',
                },
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const params = {
                            initiative_id: this.initiativeId,
                            ticketIds: this.selectedAcceptanceDeploymentTickets,
                        }
                        await this.setLoading(true);
                        const { message } = await DeploymentCenterService.submitAcceptanceDeploymentTicket(params);
                        this.hideAcceptanceDeploymentModal();
                        showToast(message, 'success');
                        await this.setLoading(false);
                        this.$emit('pageUpdated');
                    } catch (error) {
                        this.handleError(error);
                        this.resetAcceptanceDeploymentTicketList();
                    }
                } else {
                    this.resetAcceptanceDeploymentTicketList();
                }
            }).catch(() => {
                this.resetAcceptanceDeploymentTicketList();
            });
        },
        handleSelectAllAcceptanceDeploymentTickets() {
            this.selectedAcceptanceDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => {
                ticket.isChecked = this.isChkAllAcceptanceDeploymentTickets;
                if (this.isChkAllAcceptanceDeploymentTickets) {
                    this.selectedAcceptanceDeploymentTickets.push(ticket.id);
                }
                return ticket;
            });
        },
        handleSelectAcceptanceDeploymentTicket(ticket) {
            if (ticket.isChecked) {
                if (!this.selectedAcceptanceDeploymentTickets.includes(ticket.id)) {
                    this.selectedAcceptanceDeploymentTickets.push(ticket.id);
                }
            } else {
                this.selectedAcceptanceDeploymentTickets = this.selectedAcceptanceDeploymentTickets.filter(id => id !== ticket.id);
            }
            this.isChkAllAcceptanceDeploymentTickets = this.ticketList.every(ticket => ticket.isChecked);
        },
        resetAcceptanceDeploymentTicketList() {
            this.selectedAcceptanceDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
            this.isChkAllAcceptanceDeploymentTickets = false;
        },
        hideAcceptanceDeploymentModal() {
            const modalElement = document.getElementById('acceptanceDeploymentTicketsModal');
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
